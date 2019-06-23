<?php

namespace App\Repository;

use App\Entity\Counter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Counter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Counter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Counter[]    findAll()
 * @method Counter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CounterRepository extends ServiceEntityRepository
{
    /**
     * CounterRepository constructor.
     *
     * @param RegistryInterface $registry
     *
     * @throws \LogicException
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Counter::class);
    }

    /**
     * Atomic change relative value
     *
     * @param string      $type
     * @param int         $value
     * @param string|null $additional_value
     *
     * @return mixed
     */
    public function changeValueByType(string $type, int $value, string $additional_value = null)
    {
        $qb = $this->createQueryBuilder('c')
            ->update()
            ->set('c.value', 'c.value + :value')
            ->where('c.type = :type')
            ->setParameters([
                'type' => $type,
                'value' => $value
            ]);

        if ($additional_value) {
            $qb->set('c.addition_value', $additional_value);
        }

        return $qb->getQuery()->execute();
    }

    /**
     * Atomic change relative value
     *
     * @param Counter     $counter
     * @param int         $value
     * @param string|null $additional_value
     *
     * @return mixed
     */
    public function changeValue(Counter $counter, int $value, string $additional_value = null)
    {
        return $this->changeValueByType($counter->getType(), $value, $additional_value);
    }
}
