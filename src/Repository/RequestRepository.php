<?php

namespace App\Repository;

use App\Entity\Request;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Request|null find($id, $lockMode = null, $lockVersion = null)
 * @method Request|null findOneBy(array $criteria, array $orderBy = null)
 * @method Request[]    findAll()
 * @method Request[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestRepository extends ServiceEntityRepository
{
    /**
     * RequestRepository constructor.
     *
     * @param RegistryInterface $registry
     *
     * @throws \LogicException
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Request::class);
    }

    /**
     * @return Request[]
     */
    public function getRequestsWithUsers()
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.user', 'u')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param  User      $user
     * @return Request[]
     */
    public function findRequestsDonateWithUser(User $user)
    {
        return $this->createQueryBuilder('r')
            ->where('r.status = 2 AND r.user = :user')
            ->leftJoin('r.user', 'u')
            ->leftJoin('r.child', 'id')
            ->setParameters([
                'user' => $user->getId()
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @return float
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function aggregateSumSuccessPayment()
    {
        return $this->createQueryBuilder('r')
            ->select('SUM(r.sum)')
            ->where('r.status = 2')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return float
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function aggregateAvgSuccessPayment()
    {
        return $this->createQueryBuilder('r')
            ->select('AVG(r.sum)')
            ->where('r.status = 2')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
