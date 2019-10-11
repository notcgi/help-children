<?php

namespace App\EventSubscriber;

use App\Entity\SendGridSchedule;
use App\Entity\Child;
use App\Event\EmailConfirm;
use App\Event\RecurringPaymentFailure;
use App\Event\PaymentFailure;
use App\Event\RecurringPaymentRemove;
use App\Event\DonateReminderEvent;
use App\Event\PayoutRequestEvent;
use App\Event\RegistrationEvent;
use App\Event\ResetPasswordEvent;
use App\Event\FirstRequestSuccessEvent;
use App\Event\RequestSuccessEvent;
use App\Event\SendReminderEvent;
use App\Event\HalfYearRecurrentEvent;
use App\Event\YearRecurrentEvent;
use App\Repository\ChildRepository;
use App\Service\SendGridService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Psr\Log\LoggerInterface;

class SendGridSubscriber implements EventSubscriberInterface
{
    /**
     * @var SendGridService
     */
    private $sendGrid;

    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(SendGridService $sg, UrlGeneratorInterface $generator, EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->sendGrid = $sg;
        $this->generator = $generator;
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * @param RegistrationEvent $event
     *
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
                ], 0)
            ],
            'Регистрация на сайте'
        );

        $mail->setTemplateId('d-536b9f1c13cf4596a92513f67a076543');
        try {
            $this->sendGrid->send($mail);
        }
        catch (Exception $e) {
            $this->logger->error('Caught exception: '.  $e->getMessage(). "\n");
        }

        // Письмо О фонде
        $this->em->persist(
            (new SendGridSchedule())
            ->setEmail($user->getEmail())
            ->setName($user->getFirstName())
            ->setBody([
                'first_name' => $user->getFirstName()
            ])
            ->setTemplateId('d-f79a687fd8fa49089ab62a18445ce6fc')
            ->setSendAt(
                \DateTimeImmutable::createFromMutable(
                    (new \DateTime())
                    ->add(new \DateInterval('P35D'))
                    ->setTime(12, 0, 0)
                )
            )
        );

        // Писмьмо О Фонде - на что живет фонд, команда
        $this->em->persist(
            (new SendGridSchedule())
            ->setEmail($user->getEmail())
            ->setName($user->getFirstName())
            ->setBody([
                'first_name' => $user->getFirstName()
            ])
            ->setTemplateId('d-30f3d027463c430f8f743356307a77bb')
            ->setSendAt(
                \DateTimeImmutable::createFromMutable(
                    (new \DateTime())
                    ->add(new \DateInterval('P65D'))
                    ->setTime(12, 0, 0)
                )
            )
        );
    }

    public function onHalfYearRecurrent(HalfYearRecurrentEvent $event)
    {
        $req = $event->getRequest();
        $user = $req->getUser();
        $childs= $this->em->getRepository(\App\Entity\Child::class)->getOpened();
        $chnames=[];
        foreach ($childs as $child) {
            $chnames[]=$child->getName();
        }
        $mail = $this->sendGrid->getMail(
            $user->getEmail(),
            $user->getFirstName(),
            [
                'first_name' => $user->getFirstName(),
                'childs' => implode("<br>", $chnames)
            ]
        );
        $mail->setTemplateId('d-02ff4902809d434fb76e194fe6df761e');

        return $this->sendGrid->send($mail);
    }

    public function onYearRecurrent(YearRecurrentEvent $event)
    {
        $req = $event->getRequest();
        $user = $req->getUser();
        $childs= $this->em->getRepository(\App\Entity\Child::class)->getOpened();
        $chnames=[];
        foreach ($childs as $child) {
            $chnames[]=$child->getName();
        }
        $mail = $this->sendGrid->getMail(
            $user->getEmail(),
            $user->getFirstName(),
            [
                'first_name' => $user->getFirstName(),
                'childs' => implode("<br> ", $chnames)
            ]
        );
        $mail->setTemplateId('d-3d5e14962a0e4a1b9068da44577c4b83');

        return $this->sendGrid->send($mail);
    }

    /**
     * @param FirstRequestSuccessEvent $event
     *
     * @return \SendGrid\Response|void
     */
    public function onFirstRequestSuccess(FirstRequestSuccessEvent $event)
    {
        $req = $event->getRequest();
        $user = $req->getUser();
        $mail = $this->sendGrid->getMail(
            $user->getEmail(),
            $user->getFirstName(),
            [
                'first_name' => $user->getFirstName()
            ]
        );
        $mail->setTemplateId('d-07888ea4b98c44278e218c6d1f365549');

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
            $user->getFirstName(),
            [
                'first_name' => $user->getFirstName()
            ]
        );
        $mail->setTemplateId('d-92b94309494247eea3ff6187e7ddb3ae');

        return $this->sendGrid->send($mail);
    }


    public function onEmailConfirm(EmailConfirm $event)
    {
        $user = $event->getUser();
        $mail = $this->sendGrid->getMail(
            $user->getEmail(),
            $user->getFirstName(),
            [
                'first_name' => $user->getFirstName()
            ],
            'Приветствие'
        );
        $mail->setTemplateId('d-c104643da6d04f6884baf477a2f819a1');
        try {
            $this->sendGrid->send($mail);
        }
        catch (Exception $e) {
            $this->logger->error('Caught exception: '.  $e->getMessage(). "\n");
        }
    }

    public function onDonateReminder(DonateReminderEvent $event)
    {
        $user = $event->getUser();

        $this->em->persist((new SendGridSchedule())
        ->setEmail($user->getEmail())
        ->setName($user->getFirstName())
        ->setBody([
            'first_name' => $user->getFirstName(),
            'donate_url' => $this->generator->generate('donate', [
                'code' => $user->getRefCode(),
                'email' => $user->getEmail(),
                'fund' => $user->getReferrer(),
            ], 0)
        ])
        ->setTemplateId('d-7e5881310e7447599243855b1c12d2af')
        ->setSendAt(
            \DateTimeImmutable::createFromMutable(
                (new \DateTime())
                ->add(new \DateInterval('PT2H'))
            )
        ));

        $this->em->flush();
    }

    public function onPayoutRequest(PayoutRequestEvent $event)
    {
        $mail_to = 'fond.detyam@mail.ru';

        $user = $event->getUser();
        $mail = $this->sendGrid->getMail(
            $mail_to,
            $user->getFirstName(),
            [
                'first_name' => $user->getFirstName()
            ],
            'Запрос вывода средств'
        );

        $mail->addContent("text/plain",
            "Запрос: " .
            "\n Имя: " . $user->getFirstName() .
            "\n Почта: " . $user->getEmail() .
            "\n Баллы: " . $user->getRewardSum());

        try {
            $this->sendGrid->send($mail);
        }
        catch (Exception $e) {
            $this->logger->error('Caught exception: '.  $e->getMessage(). "\n");
        }
    }

    public function onResetPassword(ResetPasswordEvent $event)
    {
        $user = $event->getUser();
        $mail = $this->sendGrid->getMail(
            $user->getEmail(),
            $user->getFirstName(),
            [
                'first_name' => $user->getFirstName(),
                'reset_url' => $this->generator->generate('reset_password', [
                    'email' => $user->getEmail(),
                    'token' => $user->getPass()
                ], 0)
            ],
            'Восстановление доступа'
        );
        $mail->setTemplateId('d-d9cc7d4306914b0dbf7090e4bacae96a');
        try {
            $this->sendGrid->send($mail);
        }
        catch (Exception $e) {
            $this->logger->error('Caught exception: '.  $e->getMessage(). "\n");
        }
    }

    public function onRecurringPaymentFailure(RecurringPaymentFailure $event)
    {
        $user = $event->getRequest()->getUser();
        $mail = $this->sendGrid->getMail(
            $user->getEmail(),
            $user->getFirstName(),
            [
                'first_name' => $user->getFirstName()
            ]
        );
        $mail->setTemplateId('d-a5e99ed02f744cb1b2b8eb12ab4764b5');

        return $this->sendGrid->send($mail);
    }

    public function onPaymentFailure(PaymentFailure $event)
    {
        $user = $event->getRequest()->getUser();
        $childs= $this->em->getRepository(\App\Entity\Child::class)->getOpened();
        $chnames=[];
        foreach ($childs as $child) {
            $chnames[]=$child->getName();
        }
        $mail = $this->sendGrid->getMail(
            $user->getEmail(),
            $user->getFirstName(),
            [
                'first_name' => $user->getFirstName(),
                'childs' => implode("<br>", $chnames)
            ]
        );
        $mail->setTemplateId('d-a48d63b8f41c4020bd112a9f1ad31426');

        return $this->sendGrid->send($mail);
    }

    public function onRecurringPaymentRemove(RecurringPaymentRemove $event)
    {
        $user = $event->getRecurringPayment()->getUser();
        $childs= $this->em->getRepository(\App\Entity\Child::class)->getOpened();
        $chnames=[];
        foreach ($childs as $child) {
            $chnames[]=$child->getName();
        }
        $this->em->persist((new SendGridSchedule())
            ->setEmail($user->getEmail())
            ->setName($user->getFirstName())
            ->setBody([
                'first_name' => $user->getFirstName(),
                'childs' => implode("<br>", $chnames)
            ])
            ->setTemplateId('d-eaae4848c985425f90e2b968d9364630')
            ->setSendAt(
                \DateTimeImmutable::createFromMutable(
                    (new \DateTime())
                    ->add(new \DateInterval('P1D'))
                    ->setTime(12, 0, 0)
                )
            ));
        $this->em->flush();
    }

    public function onSendReminder(SendReminderEvent $event) {
        $today = $event->getToday();
        $childs= $this->em->getRepository(\App\Entity\Child::class)->getOpened();
        $chnames=[];
        foreach ($childs as $child) {
            $chnames[]=$child->getName();
        }
        if ($today) {
            $this->em->persist((new SendGridSchedule())
            ->setEmail($event->getEmail())
            ->setName($event->getName())
            ->setBody([
                'first_name' => $event->getName(),
                'childs' => implode("<br>", $chnames),
                'donate_url' => $this->generator->generate('donate', [
                    'email' => $event->getEmail(),
                    'name' => $event->getName(),
                    'lastName' => $event->getLastName(),
                    'phone' => $event->getPhone(),
                    'code' => $event->getCode()
                ], 0)
            ])
            ->setTemplateId('d-7e5881310e7447599243855b1c12d2af')
            ->setSendAt(
                \DateTimeImmutable::createFromMutable(
                    (new \DateTime('NOW'))
                    ->add(new DateInterval('PT2H'))
                )
            ));
        }
        else {
            $this->em->persist((new SendGridSchedule())
                ->setEmail($event->getEmail())
                ->setName($event->getName())
                ->setBody([
                    'first_name' => $event->getName(),
                    'donate_url' => $this->generator->generate('donate', [
                        'email' => $event->getEmail(),
                        'name' => $event->getName(),
                        'lastName' => $event->getLastName(),
                        'phone' => $event->getPhone(),
                        'code' => $event->getCode()
                    ], 0)
                ])
                ->setTemplateId('d-7e5881310e7447599243855b1c12d2af')
                ->setSendAt(
                    \DateTimeImmutable::createFromMutable(
                        (new \DateTime($event->getDate()))
                        ->setTime(12, 0, 0)
                    )
                ));
        }
        $this->em->flush();
        return true;
    }

    public static function getSubscribedEvents()
    {
        return [
            'donateReminderEvent' => 'onDonateReminder',
            'account.payoutRequestEvent' => 'onPayoutRequest',
            'registration' => 'onRegistration',
            'request.successFirst' => 'onFirstRequestSuccess',
            'request.success' => 'onRequestSuccess',
            'user.emailConfirm' => 'onEmailConfirm',
            'user.resetPassword' => 'onResetPassword',
            'recurring_payment.failure' => 'onRecurringPaymentFailure',
            'payment.failure' => 'onPaymentFailure',
            'recurring_payment.remove' => 'onRecurringPaymentRemove',
            'sendReminder' => 'onSendReminder',
            'halfYearRecurrent' => 'onHalfYearRecurrent',
            'yearRecurrent' => 'onYearRecurrent'
        ];
    }
}
