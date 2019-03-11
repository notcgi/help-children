<?php

namespace App\EventSubscriber;

use App\Entity\Child;
use App\Event\RequestSuccessEvent;
use App\Repository\ChildRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChildSubscriber implements EventSubscriberInterface
{
    /**
     * @var ChildRepository
     */
    private $childRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->childRepository = $em->getRepository(Child::class);
    }

    /**
     * @param RequestSuccessEvent $event
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onRequestSuccess(RequestSuccessEvent $event)
    {
        return false; // отключенно за ненадобностью

        $req = $event->getRequest();
        $child = $req->getChild();

        if (null !== $child) {
            return $this->childRepository->addCollectedNyId($child, $req->getSum());
        }

        $children = $this->childRepository->getOpened();
        $children_count = count($children);

        if (0 === $children_count) {
            return false;
        }

        $sum = $req->getSum();

        for ($i = $children_count - 1; -1 < $i; --$i) {
            $max_sum = floor($sum * 100 / ($i + 1)) / 100;
            $take_sum = $max_sum > $children[$i]->getLeftGoal() ? $children[$i]->getLeftGoal() : $max_sum;
            $sum -= $take_sum;
            $this->childRepository->addCollectedNyId($child, $take_sum);
        }

        return $children_count;
    }

    public static function getSubscribedEvents()
    {
        return [
            'request.success' => 'onRequestSuccess',
        ];
    }
}
