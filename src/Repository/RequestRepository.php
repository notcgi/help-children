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
    public function getRequestsWithUsersOld()
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.user', 'u')
            ->where('r.order_id = :order_id')
            ->orderBy('r.createdAt', 'DESC')
            ->setParameters(['order_id' => ''])
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Request[]
     */
    public function getRequestsWithUsers()
    {
        /*
        Сидя под сакурой самурай,
        Всё тщился найти просветленье,
        А пока написал костылём
          */
        $ret = $this->createQueryBuilder('r')
            ->leftJoin('r.user', 'u')
            ->where('r.order_id <> :order_id')
            ->orderBy('r.createdAt', 'DESC')
            ->setParameters(['order_id' => ''])
            ->getQuery()
            ->getResult();
        $result = array();
        foreach ($ret as $req) {
            /** @var Request $req */
            /** @var Request[] $result */
            $oid = $req->getOrder_id();
            if (empty($result[$oid])) {
                $result[$oid] = $req;
            } else {
                $sum = $result[$oid]->getSum();
                $add = $req->getSum();
                $result[$oid]->setSum($sum + $add);
            }
        }
        return $result;
    }

    /**
     * @param  User      $user
     * @return Request[]
     */
    public function findRequestsDonateWithUser(User $user)
    {
        $rows = $this->createQueryBuilder('r')
            ->where('r.status = 2 AND r.user = :user')
            ->leftJoin('r.user', 'u')
            ->leftJoin('r.child', 'id')
            ->setParameters([
                'user' => $user->getId()
            ])
            ->getQuery()
            ->getResult();
        /** @var Request[] $result */
        $result = array();
        foreach ($rows as $row) {
            /** @var Request $row */
            $dt = $row->getCreatedAt()->format('Y-m-d H:i:s');
            if (empty($result[$dt])) {
                $result[$dt] = $row;
                $result[$dt]->setChild(null);
            } else {
                $sum = $result[$dt]->getSum();
                $add = $row->getSum();
                $result[$dt]->setSum($sum + $add);
            }
        }
        return $result;
    }

    /**
     * @param User $user
     * @return float
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function aggregateSumSuccessPaymentWithUser(User $user)
    {
        return $this->createQueryBuilder('r')
            ->select('SUM(r.sum)')
            ->where('r.status = 2 AND r.user = :user')
            ->setParameters([
                'user' => $user->getId()
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param $uid
     * @return float
     */
    public function getChildrenSuccessPaymentWithUser($uid)
    {
        $q = $this->createQueryBuilder('r')
            ->select('IDENTITY(r.child)')
            ->where('r.status = 2 AND r.user = :user AND r.child is not null')
            ->groupBy('r.child')
            ->setParameters(['user' => $uid])
            ->getQuery()
            ->getResult();
        return count($q);
    }

    /**
     * @param User $user
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function aggregateCountChildWithUser(User $user)
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(DISTINCT r.child)')            
            ->where('r.status = 2 AND r.user = :user')
            ->setParameters([
                'user' => $user->getId()
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param User $user
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function aggregateCountReferWithUser(User $user)
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(DISTINCT r.user)')
            ->leftJoin('r.user', 'u')
            ->where('r.status = 2 AND u.referrer = :user')
            ->setParameters([
                'user' => $user->getId()
            ])
            ->getQuery()
            ->getSingleScalarResult();
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
