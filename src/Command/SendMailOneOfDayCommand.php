<?php

namespace App\Command;

use App\Entity\RecurringPayment;
use App\Entity\Request;
use App\Entity\User;
use App\Service\SendGridService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SendMailOneOfDayCommand extends Command
{
    protected static $defaultName = 'app:send-mail-daily';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SendGridService
     */
    private $sg;

    /**
     * SendGridScheduleCommand constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param SendGridService        $sg
     *
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(EntityManagerInterface $entityManager, SendGridService $sg)
    {
        $this->entityManager = $entityManager;
        $this->sg = $sg;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Checks needed and send SendGrid males one of day');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        // Отправка писем за день до списания рекурентного платежа
        $rus=$this->entityManager->getRepository(Request::class)->getRecRequestsWithUsers();
        $uids=[];
        foreach ($rus as $ru) {
            if(!in_array($ru['id'], $uids))  {$uids[]=$ru['id'];}
        }
        $tomorrow = (new \DateTime('tomorrow'))->format('Y-m-d');
        $rrs=[];
        $dat=[];
        $channels=[];
        $multi = curl_multi_init();
        foreach ($uids as $uid) {
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL,"https://api.cloudpayments.ru/subscriptions/find");
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_USERPWD, "pk_51de50fd3991dbf5b3610e65935d1:ecbe13569e824fa22e85774015784592");
          curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
          curl_setopt($ch, CURLOPT_POSTFIELDS, "accountId=".$uid);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          // $urrs = json_decode(curl_exec ($ch))->Model;
          curl_multi_add_handle($multi, $ch);
          $channels[$uid] = $ch;
      }
      $active = null;
        do {
            $mrc = curl_multi_exec($multi, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
         
        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($multi) == -1) {
                continue;
            }

            do {
                $mrc = curl_multi_exec($multi, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }
        foreach ($channels as $idx=>$urrs) {
            $urrs= json_decode(curl_multi_getcontent($urrs))->Model;
            if ($urrs) {
              foreach ($urrs as $urr) {
                $user=$this->entityManager->getRepository(User::class)->findOneById($idx);
                if ($tomorrow==substr($urr->NextTransactionDateIso,0,10)) {
                    $mail = $this->sg->getMail(
                        $user->getEmail(),
                        $user->getFirstName(),
                        [
                            'first_name' => $user->getFirstName(),
                            'sum' => $urr->Amount,
                        ]
                    );
                    $mail->setTemplateId('d-bc1ab47fdb6c4b73861f6bc600d8487d');
                    $this->sg->send($mail);
                    $io->text('Send mail to: '.$user->getEmail().' with template: d-bc1ab47fdb6c4b73861f6bc600d8487d');
                }
              }
          }
            curl_multi_remove_handle($multi, $channels[$idx]);
        }
         
        curl_multi_close($multi);



        // Отправка письма с поздравлением о дне рождении
        /** @var User[] $rps */
    
        $users = $this->entityManager->getRepository(User::class)->findByBirthDayToday();

        foreach ($users as $user) {
            $mail = $this->sg->getMail(
                $user->getEmail(),
                $user->getFirstName(),
                [
                    'first_name' => $user->getFirstName()
                ]
            );
            $mail->setTemplateId('d-f85328a0fe9f4ceda97d0a1af3bafaf9');
            $this->sg->send($mail);
            $io->text('Send mail to: '.$user->getEmail().' with template: d-f85328a0fe9f4ceda97d0a1af3bafaf9');
        }

        $io->success('Success');
    }
}
