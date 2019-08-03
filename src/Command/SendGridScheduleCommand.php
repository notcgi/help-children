<?php

namespace App\Command;

use App\Entity\SendGridSchedule;
use App\Service\SendGridService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SendGridScheduleCommand extends Command
{
    protected static $defaultName = 'app:sendgrid-scheduler';

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
        $this->setDescription('Checks needed and send SendGrid males');
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

        /** @var SendGridSchedule[] $scs */
        $scs = $this->entityManager->getRepository(SendGridSchedule::class)->findNeededSend();        

        foreach ($scs as $sc) {
            $mail = $this->sg->getMail(
                $sc->getEmail(),
                $sc->getName(),
                $sc->getBody()
            );
            $mail->setTemplateId($sc->getTemplateId());
            $this->sg->send($mail);
            $io->text('Send mail to: '.$sc->getEmail().' with template: '.$sc->getTemplateId());
            $this->entityManager->persist($sc->setSent(1));
        }

        $this->entityManager->flush();
        $io->success('Success');
    }
}
