<?php

namespace App\Repository;

use App\Entity\Child;
use App\Entity\ChTarget;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Child|null find($id, $lockMode = null, $lockVersion = null)
 * @method Child|null findOneBy(array $criteria, array $orderBy = null)
 * @method Child[]    findAll()
 * @method Child[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChildRepository extends ServiceEntityRepository
{
    /**
     * ChildRepository constructor.
     *
     * @param RegistryInterface $registry
     *
     * @throws \LogicException
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Child::class);
    }

    /**
     * @return Child[]
     */
    public function getOpened()
    {
        return $this->createQueryBuilder('c')
            ->where('c.goal IS NULL OR c.collected < c.goal')
            ->getQuery()
            ->getResult();
    }

    public function getCurCh($state = 'rehab') 
    {
        $val=['close'=>-1,'pmj'=>0,'rehab'=>1];
        // TODO
        /** @var EntityManager $EM */
        $EM = $this->getEntityManager();
        $DB = $EM->getConnection();

        $sql = ($state =='close') ?
        <<<sql
        SELECT * FROM children WHERE id in (SELECT m1.child
        FROM ch_target m1 LEFT JOIN ch_target m2
        ON (m1.child = m2.child AND m1.id < m2.id)
        WHERE m2.id IS NULL and m1.collected >= m1.goal AND m1.allowclose=1)
        sql

        :<<<sql
        SELECT * FROM children WHERE id in ( SELECT m1.child FROM ch_target m1 LEFT JOIN ch_target m2 ON (m1.child = m2.child AND m1.id < m2.id) WHERE ((m1.collected < m1.goal or (m1.collected >= m1.goal AND m1.allowclose=0)) and m2.id IS NULL  and m1.rehabilitation = :state))
        sql;
        $Q = $DB->prepare($sql);
        $Q->execute(['state' => $val[$state]]);
        $rows = $Q->fetchAll(\Doctrine\DBAL\FetchMode::ASSOCIATIVE);
        foreach ($rows as $key => $child) {
            $body=json_decode($child['body']);
            $rows[$key]['images']=$body->img;
            $rows[$key]['diagnosis']=$body->diagnosis;
            $rows[$key]['comment']=$body->comment;
            $rows[$key]['requisites']=$body->requisites;
            $rows[$key]['contacts']=$body->contacts;
            $rows[$key]['city']=$body->city;
            $sql=<<<sql
         SELECT * FROM ch_target WHERE child = :state
        sql;
        $Q = $DB->prepare($sql);
        $Q->execute(['state' => $child['id']]);
        $trg = $Q->fetchAll(\Doctrine\DBAL\FetchMode::ASSOCIATIVE);
            $rows[$key]['targets']=$trg;

        }
        return $rows;
    }
    /**
     * @param Child $child
     * @param float $sum
     *
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function addCollectedNyId(Child $child, float $sum)
    {
        return $this->createQueryBuilder('c')
            ->update('c.collected = c.collected + :sum')
            ->where('c.id = :id')
            ->setParameters([
                'id' => $child->getId(),
                'sum' => $sum
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function aggregateTotalCountChild()
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c)')            
            ->getQuery()
            ->getSingleScalarResult();
    }
}
