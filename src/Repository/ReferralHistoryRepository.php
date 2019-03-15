<?php

namespace App\Repository;

use App\Entity\ReferralHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ReferralHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReferralHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReferralHistory[]    findAll()
 * @method ReferralHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReferralHistoryRepository extends ServiceEntityRepository
{
    /**
     * ReferralHistoryRepository constructor.
     *
     * @param RegistryInterface $registry
     *
     * @throws \LogicException
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ReferralHistory::class);
    }

    public function findReferralsWithUser()
    {
        return $this->createQueryBuilder('r')->leftJoin('r.user', 'u')->getQuery()->getResult();
    }

    /**
     * @return float
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function aggregateSum()
    {
        return $this->createQueryBuilder('rh')
            ->select('SUM(rh.sum)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // /**
    //  * @return ReferralHistory[] Returns an array of Child objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReferralHistory
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
