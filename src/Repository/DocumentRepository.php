<?php

namespace App\Repository;

use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    /**
     * DocumentRepository constructor.
     *
     * @param RegistryInterface $registry
     *
     * @throws \LogicException
     */
    public function __construct(RegistryInterface $registry) {
        /** @noinspection PhpParamsInspection */
        parent::__construct($registry, Document::class);
    }

    public function getAll() {
        return $this->createQueryBuilder('d')
            ->getQuery()
            ->getResult();
    }
}
