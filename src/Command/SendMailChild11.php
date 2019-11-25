<?php

namespace App\Command;

use App\Entity\RecurringPayment;
use App\Entity\Request;
use App\Entity\User;
use App\Entity\Child;
use App\Service\SendGridService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SendMailChild11 extends Command
{
    protected static $defaultName = 'app:sm-child11';

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

        $userData = $this->entityManager->getRepository(Child::class)->findOneById(11); // 11 !!!

                // SEND MAIL 12
                $users = $this->entityManager->getRepository(User::class)->getAll();
                foreach ($users as $user) {
                    if($user->getEmail()!='unknown' && $user->getEmail()!='unknown2'){
                        // $io->text($user->getEmail());
                        $mail = $this->sg->getMail(
                            $user->getEmail(),
                            $user->getFirstName(),
                            [
                                'first_name' => $user    ->getFirstName(),
                                'name'       => $userData->getName(),
                                'age'        => $userData->getAge(),
                                'diag'       => $userData->getDiagnosis(),
                                'place'      => $userData->getCity(),
                                'goal'       => (int) $userData->getGoal(),
                                'photo'      => $userData->getImages()[0],
                                'id'         => $userData->getId(),
                                'url'        => $user->getDonateUrl()
                            ]
                        );
                        $mail->setTemplateId('d-8b30e88d3754462790edc69f7fe55540');
                        $this->sg->send($mail);
                    }
                }
        $io->success('Success');
    }
}
