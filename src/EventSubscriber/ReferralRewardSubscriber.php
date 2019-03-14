<?php

namespace App\EventSubscriber;

use App\Entity\ReferralHistory;
use App\Event\RequestSuccessEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReferralRewardSubscriber implements EventSubscriberInterface
{
    // @TODO: вынести констаты в отдельный конфиг
    const RECURRING_REWARD = .25;

    const DEFAULT_REWARD = .1;

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

        $this->entityManager->persist((new ReferralHistory())
            ->setUser($referrer)
            ->setDonator($user)
            ->setRequest($req)
            ->setSum(floor($req->getSum() * (
                $req->isRecurent()
                    ? self::RECURRING_REWARD
                    : self::DEFAULT_REWARD
                ) * 100) / 100)
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
