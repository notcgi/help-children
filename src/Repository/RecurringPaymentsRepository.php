<?php

namespace App\Repository;

use App\Entity\RecurringPayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RecurringPayment|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecurringPayment|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecurringPayment[]    findAll()
 * @method RecurringPayment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecurringPaymentsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RecurringPayment::class);
    }

    // /**
    //  * @return RecurringPayments[] Returns an array of RecurringPayments objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RecurringPayments
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
