<?php

namespace App\Repository;

use App\Entity\ChildHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ChildHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChildHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChildHistory[]    findAll()
 * @method ChildHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChildHistoryRepository extends ServiceEntityRepository
{
    /**
     * ChildHistoryRepository constructor.
     *
     * @param RegistryInterface $registry
     *
     * @throws \LogicException
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ChildHistory::class);
    }
}
