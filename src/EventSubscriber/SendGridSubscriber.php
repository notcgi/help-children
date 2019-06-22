<?php

namespace App\EventSubscriber;

use App\Event\EmailConfirm;
use App\Event\RegistrationEvent;
use App\Event\RequestSuccessEvent;
use App\Repository\ChildRepository;
use App\Service\SendGridService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SendGridSubscriber implements EventSubscriberInterface
{
    /**
     * @var ChildRepository
     */
    private $sendGrid;

    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    public function __construct(SendGridService $sg, UrlGeneratorInterface $generator)
    {
        $this->sendGrid = $sg;
        $this->generator = $generator;
    }

    /**
     * @param RegistrationEvent $event
     *
     * @return \SendGrid\Response
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     */
    public function onRegistration(RegistrationEvent $event)
    {
        $user = $event->getUser();
        $mail = $this->sendGrid->getMail(
            $user->getEmail(),
            $user->getFirstName(),
            [
                'first_name' => $user->getFirstName(),
                'confirm_url' => $this->generator->generate('code_confirm', [
                    'code' => $user->getRefCode(),
                    'email' => $user->getEmail()
                ])
            ]
        );
        $mail->setTemplateId('d-536b9f1c13cf4596a92513f67a076543');

        return $this->sendGrid->send($mail);
    }

    /**
     * @param RequestSuccessEvent $event
     *
     * @return \SendGrid\Response|void
     */
    public function onRequestSuccess(RequestSuccessEvent $event)
    {
        $req = $event->getRequest();
        $user = $req->getUser();
        $mail = $this->sendGrid->getMail(
            $user->getEmail(),
            $user->getFirstName()
        );
        $mail->setTemplateId(!$req->isRecurent()
            ? 'd-92b94309494247eea3ff6187e7ddb3ae'
            : 'd-07888ea4b98c44278e218c6d1f365549');

        return $this->sendGrid->send($mail);
    }

    public function onEmailConfirm(EmailConfirm $event)
    {
        $user = $event->getUser();
        $mail = $this->sendGrid->getMail(
            $user->getEmail(),
            $user->getFirstName()
        );
        $mail->setTemplateId('d-c104643da6d04f6884baf477a2f819a');

        return $this->sendGrid->send($mail);
    }

    public static function getSubscribedEvents()
    {
        return [
            'registration' => 'onRegistration',
            'request.success' => 'onRequestSuccess',
            'user.emailConfirm' => 'onEmailConfirm'
        ];
    }
}
