<?php

namespace App\EventSubscriber;

use App\Entity\ReferralHistory;
use App\Event\RequestSuccessEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReferralRewardSubscriber implements EventSubscriberInterface
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
        $user = $req->getUser();
        $referrer = $user->getReferrer();

        if (null === $referrer) {
            return;
        }

        // @TODO: вынести констаты в отдельный конфиг
        $sum = floor(($req->isRecurent() ? $req->getSum() * .25 : .1) * 100) / 100;

        $this->entityManager->persist((new ReferralHistory())
            ->setUser($referrer)
            ->setDonator($user)
            ->setRequest($req)
            ->setSum($sum)
            ->setRequest($req));

        $this->entityManager->flush();
    }

    public static function getSubscribedEvents()
    {
        return [
            'request.success' => 'onRequestSuccess',
        ];
    }
}
