<?php

namespace App\EventSubscriber;

use App\Event\RequestSuccessEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RequestSubscriber implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onRequestSuccess(RequestSuccessEvent $event)
    {
        $event->getRequest();
    }

    public static function getSubscribedEvents()
    {
        return [
           'request.success' => 'onRequestSuccess',
        ];
    }
}
