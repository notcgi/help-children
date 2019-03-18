<?php

namespace App\EventSubscriber;

use App\Entity\ReferralHistory;
use App\Entity\User;
use App\Event\RequestSuccessEvent;
use App\Repository\UserRepository;
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

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
        $this->userRepository = $em->getRepository(User::class);
    }

    /**
     * @param RequestSuccessEvent $event
     *
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

        $sum = floor(
            $req->getSum() * (
                $req->isRecurent()
                    ? self::RECURRING_REWARD
                    : self::DEFAULT_REWARD
                ) * 100
            ) / 100;
        $this->entityManager->persist(
            (new ReferralHistory())
                ->setUser($referrer)
                ->setDonator($user)
                ->setRequest($req)
                ->setSum($sum)
                ->setRequest($req)
        );
        $this->userRepository->addReferralReward($referrer, $sum);
        $this->entityManager->flush();
    }

    public static function getSubscribedEvents()
    {
        return [
            'request.success' => 'onRequestSuccess'
        ];
    }
}
