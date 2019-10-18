<?php

namespace App\Repository;

use App\Entity\Request;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
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
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
    /**
     * @return Request[]
     */
    public function getRecRequestsWithUsers()
    {
        return $this->createQueryBuilder('r')
            ->select('u.id')
            ->leftJoin('r.user', 'u')
            ->where('r.recurent=1')
            // ->where('r.status = 2 AND  r.recurent=1')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_SCALAR);
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
            ->setParameters(['user' => $user->getId()])
            ->getQuery()
            ->getResult();
    }

       /**
     * @param  User      $user
     * @return Request[]
     */
    public function findRequestsWithUser(User $user)
    {
        return $this->createQueryBuilder('r')
            ->where('r.status = 2 AND r.user = :user')
            ->setParameters(['user' => $user->getId()])
            ->getQuery()
            ->getResult();
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
            ->setParameters(['user' => $user->getId()])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param $uid
     * @return float
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getChildrenSuccessPaymentWithUser($uid)
    {
        // TODO
        /** @var EntityManager $EM */
        $EM = $this->getEntityManager();
        $DB = $EM->getConnection();
        $sql = <<<sql
select
  tm.child   as child,
  tm.request as request
from children_requests as tm
left join requests as tr on (tr.id = tm.request)
where tr.status = 2 and tr.user_id = :user
sql;
        $Q = $DB->prepare($sql);
        $Q->execute(['user' => $uid]);
        $rows = $Q->fetchAll(\Doctrine\DBAL\FetchMode::ASSOCIATIVE);
        $ret  = array();
        foreach ($rows as $row) {
            $cid = intval($row['child']);
            if (in_array($cid, $ret)) continue;
            $ret[] = $cid;
        }
        return count($ret);
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
