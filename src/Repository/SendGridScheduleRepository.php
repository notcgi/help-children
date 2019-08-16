<?php

namespace App\Repository;

use App\Entity\SendGridSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SendGridSchedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method SendGridSchedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method SendGridSchedule[]    findAll()
 * @method SendGridSchedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SendGridScheduleRepository extends ServiceEntityRepository
{
    /**
     * SendGridScheduleRepository constructor.
     *
     * @param RegistryInterface $registry
     *
     * @throws \LogicException
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SendGridSchedule::class);
    }

    public function findNeededSend(): array
    {
        return $this->createQueryBuilder('sgs')
            ->where('sgs.sendAt <= :sendAt AND sgs.sent=0')            
            ->setParameter('sendAt', new \DateTime())
            ->getQuery()
            ->getResult();
    }

    public function findUnfinished($email): array
    {
        return $this->createQueryBuilder('sgs')
            ->where('sgs.email = :email AND sgs.sent=0 AND sgs.template_id=:template')            
            ->setParameters([
                'email' => $email,
                'template' => 'd-a5e99ed02f744cb1b2b8eb12ab4764b5'
            ])
            ->getQuery()
            ->getResult();
    }

    public function changeEmail($old_email, $email): void
    {
        $this->createQueryBuilder('sgs')
            ->update('App\Entity\SendGridSchedule', 'sgs')
            ->set('sgs.email', '?1')                
            ->where('sgs.email = ?2')
            ->setParameter(1, $email)
            ->setParameter(2, $old_email)                
            ->getQuery()->execute();        
    }
}
