<?php

namespace App\Repository;

use App\Entity\Child;
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
}
