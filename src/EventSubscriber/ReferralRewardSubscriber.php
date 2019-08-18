<?php

namespace App\EventSubscriber;

use App\Entity\Config;
use App\Entity\ReferralHistory;
use App\Entity\User;
use App\Event\FirstRequestSuccessEvent;
use App\Event\RequestSuccessEvent;
use App\Repository\ConfigRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReferralRewardSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ConfigRepository
     */
    private $configRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
        $this->userRepository = $em->getRepository(User::class);
        $this->configRepository = $em->getRepository(Config::class);
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

        $config = $this->configRepository->find(1);
        $sum = floor(
            $req->getSum() * (
                $req->isRecurent()
                    ? $config->getPercentRecurrent()
                    : $config->getPercentDefault()
                ) * 100
            ) / 100;
        $this->entityManager->persist(
            (new ReferralHistory())
                ->setUser($referrer)
                ->setDonator($user)
                ->setRequest($req)
                ->setSum($sum)
        );
        $this->userRepository->addReferralReward($referrer, $sum);
        $this->entityManager->flush();
    }


    /**
     * @param FirstRequestSuccessEvent $event
     *
     * @throws \Exception
     */
    public function onFirstRequestSuccess(FirstRequestSuccessEvent $event): void
    {
        $req = $event->getRequest();
        $user = $req->getUser();
        $referrer = $user->getReferrer();

        if (null === $referrer) {
            return;
        }

        $config = $this->configRepository->find(1);
        $sum = floor(
            $req->getSum() * (
                $req->isRecurent()
                    ? $config->getPercentRecurrent()
                    : $config->getPercentDefault()
                ) * 100
            ) / 100;
        $this->entityManager->persist(
            (new ReferralHistory())
                ->setUser($referrer)
                ->setDonator($user)
                ->setRequest($req)
                ->setSum($sum)
        );
        $this->userRepository->addReferralReward($referrer, $sum);
        $this->entityManager->flush();
    }


    public static function getSubscribedEvents()
    {
        return [
            'request.success' => 'onRequestSuccess',
            'request.successFirst' => 'onFirstRequestSuccess',
            'recurringRequest.success' => 'onRequestSuccess'
        ];
    }
}
