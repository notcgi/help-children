<?php

namespace App\Repository;

use App\Entity\SendGridSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SendGridSchedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method SendGridSchedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method SendGridSchedule[]    findAll()
 * @method SendGridSchedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SendGridScheduleRepository extends ServiceEntityRepository
{
    /**
     * SendGridScheduleRepository constructor.
     *
     * @param RegistryInterface $registry
     *
     * @throws \LogicException
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SendGridSchedule::class);
    }

    /**
     * @return SendGridSchedule[]
     * @throws \Exception
     */
    public function findNeededSend(): array
    {
        return $this->createQueryBuilder('sgs')
            ->where('sgs.sendAt <= :sendAt')
            ->setMaxResults(50)
            ->setParameter('sendAt', new \DateTime())
            ->getQuery()
            ->getResult();
    }
}
