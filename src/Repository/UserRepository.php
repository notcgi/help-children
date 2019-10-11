<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

// use Scienta\DoctrineJsonFunctions\Query\AST\Functions\Mysql as DqlFunctions;

// $config = new \Doctrine\ORM\Configuration();
// $config->addCustomStringFunction(DqlFunctions\JsonExtract::FUNCTION_NAME, DqlFunctions\JsonExtract::class);
// $config->addCustomStringFunction(DqlFunctions\JsonValue::FUNCTION_NAME, DqlFunctions\JsonValue::class);

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

    /**
     * Возвращает рефёрерров с суммами вознаграждений и их пожертвованиями
     *
     * @param  User  $user
     * @return mixed
     */
    public function findReferralsWithSum(User $user)
    {
        return $this->createQueryBuilder('u')
            ->select('u.email, u.meta, u.createdAt')        
            ->addSelect('(SELECT rh.sum FROM \App\Entity\ReferralHistory rh WHERE rh.donator=u GROUP BY rh.donator) as reward')            
            ->addSelect('(SELECT SUM(r.sum) FROM \App\Entity\Request r WHERE r.status=2 AND r.user=u) as donate')
            ->where('u.referrer = :id')
            ->setParameters([
                'id' => $user->getId()
            ])
            ->getQuery()
            ->getResult();
    }

    public function getWithReferralSum()
    {
        return $this->createQueryBuilder('u')
            ->addSelect('SUM(rh.sum)')
            ->leftJoin('u.referral_history', 'rh')
            ->groupBy('u.id')
            ->getQuery()
            ->getResult();
    }

    public function getAll()
    {
        return $this->createQueryBuilder('u')            
            ->getQuery()
            ->getResult();
    }

    public function getDonatorRewards($user)
    {
        return $this->createQueryBuilder('u')
            ->select('u.id')
            ->addSelect('rh.sum')
            ->leftJoin('u.referral_history', 'rh')            
            ->where('rh.user = :id')
            ->groupBy('rh.donator')
            ->setParameter('id', $user->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $user
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findWithReferralSum(User $user)
    {
        return $this->createQueryBuilder('u')
            ->addSelect('SUM(rh.sum)')
            ->leftJoin('u.referral_history', 'rh')
            ->where('u.id = :id')
            ->groupBy('u.id')
            ->setParameters([
                'id' => $user->getId()
            ])
            ->getQuery()
            ->getSingleResult();
    }

    public function findByBirthDayToday()
    {
        return $this->createQueryBuilder('u')
            ->where('MONTH(u.birthday) = MONTH(:date) AND DAY(u.birthday) = DAY(:date)')
            ->setParameter('date', new \DateTime())
            ->getQuery()
            ->getResult();
    }

    // public function findByPhone()
    // {
    //     return $this->createQueryBuilder('u')
    //         ->where('JSON_VALUE(u.meta, "$.phone") = "+79999999999"')
    //         // ->setParameter('phone', $phone)
    //         ->getQuery()
    //         ->getResult();
    // }

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


    // public function findChildren($uid)
    // {
    //     return $this->createQuery('SELECT distinct c.name FROM children_requests as cr left join requests as r on cr.request = r.id right join children as c on cr.child=c.id where r.user_id='.$uid)->execute();
    // }

//SELECT r.user_id, r.sum, c.name FROM requests as r right join children as c on r.child_id=c.id 
//SELECT distinct c.name FROM children_requests as cr left join requests as r on cr.request = r.id right join children as c on cr.child=c.id where r.user_id=871

}
