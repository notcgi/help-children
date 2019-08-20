<?php

namespace App\EventSubscriber;

use App\Entity\RecurringPayment;
use App\Event\RequestSuccessEvent;
use App\Event\FirstRequestSuccessEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RecurringPaymentSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
    }

    /**
     * @param  RequestSuccessEvent $event
     * @throws \Exception
     */
    public function onRequestSuccess(RequestSuccessEvent $event): void
    {
        $req = $event->getRequest();

        if (!$req->isRecurent()) {
            return;
        }

        $this->entityManager->persist((new RecurringPayment())
            ->setRequest($req)
            ->setWithdrawalAt(new \DateTime()));

        $this->entityManager->flush();
    }

    /**
     * @param  FirstRequestSuccessEvent $event
     * @throws \Exception
     */
    public function onFirstRequestSuccess(FirstRequestSuccessEvent $event): void
    {
        $req = $event->getRequest();

        if (!$req->isRecurent()) {
            return;
        }

        $this->entityManager->persist((new RecurringPayment())
            ->setRequest($req)
            ->setWithdrawalAt(new \DateTime()));

        $this->entityManager->flush();
    }

    public static function getSubscribedEvents()
    {
        return [
            'request.success' => 'onRequestSuccess',
            'request.successFirst' => 'onFirstRequestSuccess'
        ];
    }
}
