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
    /**
     * RecurringPaymentsRepository constructor.
     *
     * @param RegistryInterface $registry
     *
     * @throws \LogicException
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RecurringPayment::class);
    }

    /**
     * @return float
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function aggregateSum()
    {
        return $this->createQueryBuilder('rp')
            ->select('SUM(r.sum)')
            ->leftJoin('rp.request', 'r')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param int $limit
     *
     * @return mixed
     * @throws \Exception
     */
    public function findNeedRequest($limit = 5)
    {
        return $this->createQueryBuilder('rp')
            ->leftJoin('rp.request', 'r')
            ->where('rp.withdrawalAt <= :date')
            ->setParameter('date', (new \DateTime())->sub(new \DateInterval('P1M')))
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
