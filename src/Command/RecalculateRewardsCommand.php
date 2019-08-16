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
        return;
        $repository = $this->entityManager->getRepository(User::class);
        $io = new SymfonyStyle($input, $output);        
        $users = $repository->getAll();

        foreach ($users as $user) {
            $userRewards = $repository->getDonatorRewards($user);
            $rightRewardSum = array_sum(array_map(function($item) { 
                return $item['sum']; 
            }, $userRewards));

            if ($user->getRewardSum() == $rightRewardSum)
                continue;

            $io->warning(
                'Fix reward user ' . $user->getId() . ': ' . $user->getEmail() . ' ' . $user->getRewardSum() . ' to ' . $rightRewardSum
            );

            $user->setRewardSum((float) $rightRewardSum);
            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();
        $io->success('Success');
    }
}
