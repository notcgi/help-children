<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     *
     * @param RegistryInterface $registry
     *
     * @throws \LogicException
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findReferralsWithHistory(User $user)
    {
        return $this->createQueryBuilder('u')
            ->addSelect('SUM(rh.sum)')
            ->where('u.referrer = :id')
            ->leftJoin('u.donate_history', 'rh')
            ->groupBy('rh.donator')
            ->setParameters([
                'id' => $user->getId()
            ])
            ->getQuery()
            ->getResult();
    }

    public function findReferralsWithSum(User $user)
    {
        return $this->createQueryBuilder('u')
            ->addSelect('SUM(rh.sum)')
            ->where('u.referrer = :id')
            ->leftJoin('u.donate_history', 'rh')
            ->setParameters([
                'id' => $user->getId()
            ])
            ->getQuery()
            ->getResult();
    }

    public function findUserSelecting(int $id)
    {
        return $this->createQueryBuilder('p')
            ->where('p.id != :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function addReferralReward(User $user, float $sum)
    {
        return $this->createQueryBuilder('u')
            ->update()
            ->set('u.rewardSum', 'u.rewardSum + :sum')
            ->where('u.id = :id')
            ->setParameters([
                'id' => $user->getId(),
                'sum' => $sum
            ])
            ->getQuery()
            ->execute();
    }

    /**
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countReferrerNotNull()
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.referrer IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
