<?php

namespace App\Command;

use App\Entity\RecurringPayment;
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
        /** @var RecurringPayment[] $rps */
        $rps = $this->entityManager->getRepository(RecurringPayment::class)->findBeforeNeedOneDayRequest();        
        foreach ($rps as $rp) {
            $user = $rp[0]->getUser();
            $mail = $this->sg->getMail(
                $user->getEmail(),
                $user->getFirstName(),
                [
                    'first_name' => $user->getFirstName(),
                    'sum' => number_format($rp['sum'], 2, '.', ' '),
                ]
            );
            $mail->setTemplateId('d-bc1ab47fdb6c4b73861f6bc600d8487d');
            $this->sg->send($mail);            
            $io->text('Send mail to: '.$user->getEmail().' with template: d-bc1ab47fdb6c4b73861f6bc600d8487d');
        }

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
