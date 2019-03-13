<?php

namespace App\EventSubscriber;

use App\Entity\RecurringPayment;
use App\Event\RequestSuccessEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RecurringPaymentSubscriber implements EventSubscriberInterface
{
    /**
     * @var RecurringPayment
     */
    private $childRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $em)
    {
        $this->childRepository = $em->getRepository(RecurringPayment::class);
        $this->entityManager = $em;
    }

    /**
     * @param RequestSuccessEvent $event
     * @return void
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

    public static function getSubscribedEvents()
    {
        return [
            'request.success' => 'onRequestSuccess',
        ];
    }
}
