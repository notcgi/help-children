<?php

namespace App\Repository;

use App\Entity\ChTarget;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ChTarget|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChTarget|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChTarget[]    findAll()
 * @method ChTarget[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChTargetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChTarget::class);
    }

    // /**
    //  * @return ChTarget[] Returns an array of ChTarget objects
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
    public function findOneBySomeField($value): ?ChTarget
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
