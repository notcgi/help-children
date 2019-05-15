<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RecalculateRewardsCommand extends Command
{
    protected static $defaultName = 'app:recalculate-rewards';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * RecalculateRewardsCommand constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Recalculate true rewards');
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
        $rh = $this->entityManager->getRepository(User::class)->getWithReferralSum();

        foreach ($rh as $v) {
            if (null === $v[1] || $v[0]->getRewardSum() == $v[1]) {
                continue;
            }

            $io->warning(
                'Fix reward user '.$v[0]->getId().': '.$v[0]->getEmail().' '.$v[0]->getRewardSum().' to '.$v[1]
            );

            $v[0]->setRewardSum((float) $v[1]);
            $this->entityManager->persist($v[0]);
        }

        $this->entityManager->flush();
        $io->success('Success');
    }
}
