<?php

namespace App\Repository;

use App\Entity\Config;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Config|null find($id, $lockMode = null, $lockVersion = null)
 * @method Config|null findOneBy(array $criteria, array $orderBy = null)
 * @method Config[]    findAll()
 * @method Config[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfigRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Config::class);
    }
}
