<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\SendGridSchedule;
use App\Event\FirstRequestSuccessEvent;
use App\Event\RequestSuccessEvent;
use App\Event\RecurringPaymentFailure;
use App\Event\PaymentFailure;
use App\Event\SendReminderEvent;
use App\Event\HalfYearRecurrentEvent;
use App\Event\YearRecurrentEvent;
use App\Service\UnitellerService;
use App\Service\UsersService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
// use Scienta\DoctrineJsonFunctions\Query\AST\Functions\Mariadb as DqlFunctions;
// use Scienta\DoctrineJsonFunctions\Query\AST\Functions\Mariadb\JsonValue;


class DonateController extends AbstractController
{
    const REF_RATE = .06;

    private $gmm;

    public function __construct($gmm)
    {
        $this->gmm = $gmm;
    }
    /**
     * @param Request          $request
     * @return Response
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \Exception
     */
    public function sms(Request $request)
    {
        $EM  = $this->getDoctrine()->getManager();
        $order_id   = $request->request->get('order_id');
        $data       = $request->request->get('data');
        $phone      = $request->request->get('phone');
        $operator   = $request->request->get("operator");
        $amount     = $request->request->get('amount');
        $sign       = $request->request->get('sign');
        $key=$this->getParameter('gmm');
        $osign=md5($order_id.$data.$phone.$operator.$amount.$key);
            $fp = fopen('/home/children/help-children/sms_req.txt', 'a');
            fwrite($fp, json_encode($request->request->all()).$osign.PHP_EOL);
            fclose($fp);
        if ($sign!==$osign){
            return new Response(json_encode(["status"=>'fail']), Response::HTTP_OK, ['content-type' => 'text/html']);
        } else{
            $user=$EM->createQuery("SELECT u FROM App\\Entity\\User u WHERE JSON_VALUE(u.meta, '$.phone') = ".$phone)
                ->getResult()[0];
            $req = new \App\Entity\Request();
                    $req->setSum($amount)
                        ->setUser($user)
                        ->setStatus(2)
                        ->setJson(["payment-type"=>"sms"])
                        ->setOrder_id('');
                $this->referralHistory($req);
                    $EM->persist($req);
                    $EM->flush();

            $children = $EM->getRepository(\App\Entity\Child::class)->getOpened();
            $ti = array();
            foreach ($children as $child) $ti[] = '('.$child->getId().','.$req->getId().','.$req->getSum().')';
            $sql = 'insert into children_requests (`child`,`request`, `sum`) values '.implode(',', $ti);
            $EM->getConnection()->prepare($sql)->execute();
            return new Response(json_encode(["status"=>'ok']), Response::HTTP_OK, ['content-type' => 'text/html']);
        }
    }



    /**
     * @param Request          $request
     * @return Response
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \Exception
     */
    public function ok(Request $request)
    {

        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($request->isMethod('post')) {
            $id  = $request->request->get('order_id');
            /** @var EntityManager $EM */
            $EM  = $this->getDoctrine()->getManager();
            $req = $EM->getRepository(\App\Entity\Request::class)->find($id);
            if (!$req) return new Response('order not found', 404);
            $children = $EM->getRepository(\App\Entity\Child::class)->getOpened();
            $ti = array();
            foreach ($children as $child) $ti[] = '('.$child->getId().','.$req->getId().','.$req->getSum().')';
            $sql = 'insert into children_requests (`child`,`request`, `sum`) values '.implode(',', $ti);
            $EM->getConnection()->prepare($sql)->execute();
            $req->setStatus(2);
            $this->referralHistory($req);
            $EM->persist($req);
            $EM->flush();
            return new Response(json_encode(["code"=>'ok']), Response::HTTP_OK, ['content-type' => 'text/html']);
        } else{
            $id  = $request->query->get('Order_ID');
            /** @var EntityManager $EM */
            $EM  = $this->getDoctrine()->getManager();
            $req = $EM->getRepository(\App\Entity\Request::class)->find($id);
            if (!$req) return new Response('order not found', 404);
            $children = $EM->getRepository(\App\Entity\Child::class)->getOpened();
            $ti = array();
            foreach ($children as $child) $ti[] = '('.$child->getId().','.$req->getId().','.$req->getSum().')';
            $sql = 'insert into children_requests (`child`,`request`, `sum`) values '.implode(',', $ti);
            $EM->getConnection()->prepare($sql)->execute();
            $req->setStatus(2);
            $this->referralHistory($req);
            $EM->persist($req);
            $EM->flush();
            return $this->redirectToRoute('account_history');
        }

        #help https://symfony.com/doc/current/components/http_foundation.html
        // return new Response(json_encode(["code"=>$sql]), Response::HTTP_OK, ['content-type' => 'text/html']);
        return $this->redirectToRoute('account_history');
    }

    /**
     * @param Request          $request
     * @return Response
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \Exception
     */
    public function no(Request $request, EventDispatcherInterface $dispatcher)
    {
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($request->isMethod('post')) {
            $id  = $request->request->get('order_id');
            $EM  = $this->getDoctrine()->getManager();
            $req = $this->getDoctrine()->getRepository(\App\Entity\Request::class)->find($id);
            if (!$req) return new Response('order not found', 404);
            $req->setStatus(1);
            $EM->persist($req);
            $EM->flush();
            return new Response(json_encode(["code"=>'0']), Response::HTTP_OK, ['content-type' => 'text/html']);
        }
        if ($request->isMethod('get')) {
            $id  = $request->query->get('Order_ID');
            $EM  = $this->getDoctrine()->getManager();
            $req = $EM->getRepository(\App\Entity\Request::class)->find($id);
            $jsn = json_decode($req->getJson());
            $data=[
                "email"     => $jsn->email,
                "name"      => $jsn->name,
            // "lastname"   => $jsn->lastname,
                "firstname"   => $jsn->surname,
                "recurent"     => $jsn->recurent,
                "phone"     => $jsn->phone,
                "sum"     => $jsn->sum,
                "payment-type"     => $jsn->{'payment-type'},
                "EMoneyType"     => $jsn->EMoneyType
            ];
            if (!$req) return new Response('order not found', 404);
            $req->setStatus(1);
            $EM->persist($req);
            $EM->flush();
            // if ($req -> isRecurent()) {
            // $dispatcher->dispatch(new RecurringPaymentFailure($req), RecurringPaymentFailure::NAME);}
            // else{
                $dispatcher->dispatch(new PaymentFailure($req), PaymentFailure::NAME);//}
            // return new Response(json_encode(["code"=>'0']), Response::HTTP_OK, ['content-type' => 'text/html']);
            return $this->redirectToRoute('donate', $data);
        }
        #help https://symfony.com/doc/current/components/http_foundation.html
        // return $this->redirectToRoute('account_history');
    }

    /**
     * @param Request                  $request
     * @param EventDispatcherInterface $dispatcher
     * @return Response
     * @throws \Exception
     */
    public function fail(Request $request, EventDispatcherInterface $dispatcher)
    {
        $form = $request->request->all();

        file_put_contents(dirname(__DIR__)."/../var/logs/fail.log", date("d.m.Y H:i:s")."; ".print_r($request->request->all(), true)."\n", FILE_APPEND);# FILE_APPEND | LOCK_EX

        $entityManager = $this->getDoctrine()->getManager();
        $user_id = $form['AccountId'];
        $user = $entityManager->getRepository(User::class)->find($user_id);
        $req = (new \App\Entity\Request())->setUser($user);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        if ($req -> isRecurent()) {
            $dispatcher->dispatch(new RecurringPaymentFailure($req), RecurringPaymentFailure::NAME);}
        else{
                $dispatcher->dispatch(new PaymentFailure($req), PaymentFailure::NAME);}

        // Убрать напоминание о завершении платежа
        $urs = $entityManager->getRepository(SendGridSchedule::class)->findUnfinished($req->getUser()->getEmail());
        foreach ($urs as $ur) {
            $entityManager->remove($ur);
        }
        $entityManager->flush();

        return new Response(json_encode(["code"=>'0']), Response::HTTP_OK, ['content-type' => 'text/html']);
    }

    /**
     *
     * @param Request                  $request
     * @param UnitellerService         $unitellerService
     * @param EventDispatcherInterface $dispatcher
     * @return Response
     * @throws \Exception
     */
    public function status(Request $request, UnitellerService $unitellerService, EventDispatcherInterface $dispatcher)
    {
        $form = $request->request->all();

        $entityManager = $this->getDoctrine()->getManager();
        /** @var \App\Entity\Request $req */
        if (array_key_exists('SubscriptionId', $form)){
            $req = $entityManager->getRepository(\App\Entity\Request::class)->find($form['InvoiceId']);

            // Если не нашёл такого платежа - возможно, он рекуррентный
            if (!$req) {
                $subscription_id = $form['SubscriptionId'];
                if (null === $subscription_id)
                    return new Response('', 404); // Если нет Id подписки, всё-таки ерунда
                $subscr_req = $entityManager->getRepository(\App\Entity\Request::class)->findOneBy([
                        'SubscriptionsId' => $subscription_id
                    ]);
                if (!$subscr_req)
                    return new Response('', 404); // Не судьба...
                $rp = $entityManager->getRepository(\App\Entity\RecurringPayment::class)->find($subscr_req->getId());
                if (!$rp) {
                    file_put_contents(dirname(__DIR__)."/../var/logs/status.log", date("d.m.Y H:i:s")."; POST ".print_r($_POST, true). "\n GET ".print_r($_GET, true)."\n form UNREGISTERED IN SYSTEM".print_r($form, true)."\n", FILE_APPEND);
                    return new Response('', 404); // Незарегистрированная в базе подписка
                }

                $req = new \App\Entity\Request();
                $req->setChild($subscr_req->getChild())
                    ->setUser($subscr_req->getUser())
                    ->setSum($subscr_req->getSum())
                    ->setTransactionId($form['TransactionId'])
                    // ->setJson(json_encode($form))
                    ->setOrder_id('')
                    ->setStatus(2)
                    ->setRecurent(0);

                $rp->setWithdrawalAt(new \DateTime());
                $entityManager->persist($req);
                $entityManager->persist($rp);
                $entityManager->flush();
                /** @noinspection PhpMethodParametersCountMismatchInspection */
                $dispatcher->dispatch(new RequestSuccessEvent($req), RequestSuccessEvent::NAME);

                $startDate = (new \DateTime($rp->getCreatedAt()->format('Y-m-d')))->getTimestamp();
                $endDate = (new \DateTime())->getTimestamp();
                $numberOfMonths = abs((date('Y', $endDate) - date('Y', $startDate))*12 + (date('m', $endDate) - date('m', $startDate)));

                if ($numberOfMonths == 6)
                    /** @noinspection PhpMethodParametersCountMismatchInspection */
                    /** @noinspection PhpParamsInspection */
                    $dispatcher->dispatch(new HalfYearRecurrentEvent($req), HalfYearRecurrentEvent::NAME);

                if ($numberOfMonths == 12)
                    /** @noinspection PhpMethodParametersCountMismatchInspection */
                    /** @noinspection PhpParamsInspection */
                    $dispatcher->dispatch(new YearRecurrentEvent($req), YearRecurrentEvent::NAME);

                return new Response(json_encode(["code"=>'0']), Response::HTTP_OK, ['content-type' => 'text/html']);
            }

            file_put_contents(dirname(__DIR__)."/../var/logs/status.log", date("d.m.Y H:i:s")."; POST ".print_r($_POST, true). "\n GET ".print_r($_GET, true)."\n form".print_r($form, true)."\n", FILE_APPEND);

            switch ($form['Status']) {
                #case 'paid':
                #case 'authorized':
                case 'Completed':
                    $req->setStatus(2);
                    $req->setTransactionId($form['TransactionId']); #avtorkoda
                    // $req->setJson(json_encode($form));              #avtorkoda

                    // Убрать напоминание о завершении платежа
                    $urs = $entityManager->getRepository(SendGridSchedule::class)->findUnfinished($req->getUser()->getEmail());
                    foreach ($urs as $ur) {
                        $entityManager->remove($ur);
                    }
                    $children = $EM->getRepository(\App\Entity\Child::class)->getOpened();
                    $ti = array();
                    foreach ($children as $child) $ti[] = '('.$child->getId().','.$req->getId().','.$req->getSum().')';
                    $sql = 'insert into children_requests (`child`,`request`, `sum`) values '.implode(',', $ti);
                    $EM->getConnection()->prepare($sql)->execute();
                    $entityManager->flush();
                    $user_requests = $entityManager->getRepository(\App\Entity\Request::class)->findRequestsWithUser($req->getUser());
                    if (count($user_requests) > 1) {
                        /** @noinspection PhpMethodParametersCountMismatchInspection */
                        $dispatcher->dispatch(new RequestSuccessEvent($req), RequestSuccessEvent::NAME);
                    }
                    else {
                        /** @noinspection PhpMethodParametersCountMismatchInspection */
                        $dispatcher->dispatch(new FirstRequestSuccessEvent($req), FirstRequestSuccessEvent::NAME);
                        if (!$req->isRecurent()) {
                            // Письмо №10
                            $user = $req->getUser();
                            $entityManager->persist(
                                (new SendGridSchedule())
                                ->setEmail($user->getEmail())
                                ->setName($user->getFirstName())
                                ->setBody([
                                    'first_name' => $user->getFirstName()
                                ])
                                ->setTemplateId('d-1836d6b43e9c437d8f7e436776d1a489')
                                ->setSendAt(
                                    \DateTimeImmutable::createFromMutable(
                                        (new \DateTime())
                                        ->add(new \DateInterval('P28D'))
                                        ->setTime(12, 0, 0)
                                    )
                                )
                            );
                            $entityManager->flush();
                        }
                    }

                    if ($req -> isRecurent()) {//оформление подписки

                        $subscription_id = $form['SubscriptionId'];
                        $req->setSubscriptionsId($subscription_id);

                        file_put_contents(dirname(__DIR__)."/../var/logs/recurent.log", date("d.m.Y H:i:s")."; POST ".print_r($_POST, true). "\n GET ".print_r($_GET, true)."\n", FILE_APPEND);

                        $user = $req->getUser();
                        // Увеличение
                        $entityManager->persist(
                            (new SendGridSchedule())
                            ->setEmail($user->getEmail())
                            ->setName($user->getFirstName())
                            ->setBody([
                                'first_name' => $user->getFirstName()
                            ])
                            ->setTemplateId('d-b12bbbbdfd2c4747b6b96b2243ffaad7')
                            ->setSendAt(
                                \DateTimeImmutable::createFromMutable(
                                    (new \DateTime())
                                    ->add(new \DateInterval('P4M3D'))
                                    ->setTime(12, 0, 0)
                                )
                            )
                        );
                        $entityManager->flush();
                    }
                break;
                case 'canceled':// ?
                    $req->setStatus(1);
            }

            $entityManager->persist($req->setUpdatedAt(new \DateTime()));
            $entityManager->flush();

            #help https://symfony.com/doc/current/components/http_foundation.html
            return new Response(json_encode(["code"=>'0']), Response::HTTP_OK, ['content-type' => 'text/html']);
        }
        else{
            if ($unitellerService->validateStatusSignature($form)){
                file_put_contents(dirname(__DIR__)."/../var/logs/status_uni.log", date("d.m.Y H:i:s")."; POST ".print_r($_POST, true). "\n GET ".print_r($_GET, true)."\n form".print_r($form, true)."\n", FILE_APPEND);
                $req = $entityManager->getRepository(\App\Entity\Request::class)->find($form['Order_ID']);
                if (!$req) return new Response('order not found', 404);
                if($form['Status']=='paid'){
                    $children = $entityManager->getRepository(\App\Entity\Child::class)->getOpened();
                    $ti = array();
                    foreach ($children as $child) $ti[] = '('.$child->getId().','.$req->getId().','.$req->getSum().')';
                    $sql = 'insert into children_requests (`child`,`request`, `sum`) values '.implode(',', $ti);
                    $entityManager->getConnection()->prepare($sql)->execute();
                    $req->setStatus(2);
                    $this->referralHistory($req);
                    $entityManager->persist($req);
                    $entityManager->flush();
                }
                return new Response(json_encode(['status'=>'ok']), Response::HTTP_OK, ['content-type' => 'text/html']);
            } else{
                return new Response(json_encode(['status'=>'not valid sign']), Response::HTTP_BAD_REQUEST, ['content-type' => 'text/html']);
            }
        }
    }

    /**
     * @param Request                   $request
     * @param UsersService              $usersService
     * @param UnitellerService          $unitellerService
     * @param SessionInterface          $session
     * @param EventDispatcherInterface  $dispatcher
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator    $authenticator
     * @return Response
     * @throws \LogicException
     * @throws \RuntimeException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Exception
     */
    public function main(
        Request $request,
        UsersService $usersService,
        UnitellerService $unitellerService,
        SessionInterface $session,
        EventDispatcherInterface $dispatcher,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator
    ) {
        $name = $request->query->get('name');
        $email = $request->query->get('email');
        $code = $request->query->get('code');
        $firstName = $request->query->get('firstname');
        $phone = $request->query->get('phone');

            $phone = preg_replace(
                '/^[78]/',
                '+7',
                $phone
            );
        if (!$this->isGranted('ROLE_USER') && isset($code)) {
            $doctrine = $this->getDoctrine();
            $user = $doctrine->getRepository(User::class)->findOneBy([
                'ref_code' => $code,
                'email' => $email
            ]);

            if ($user) {
                $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main' // firewall name in security.yaml
                );
            }
        }

        $user = $this->getUser();
        $form_errors = [];
        $auth_errors='';
        $child_id = (int) $request->request->filter('child_id', null, FILTER_VALIDATE_INT);
        $form = [
            'payment-type' => trim($request->request->get('payment-type', $request->query->get('payment-type') ?? 'visa')),
            'EMoneyType'   =>      $request->request->get('EMoneyType', $request->query->get('EMoneyType') ),
            'child_id'     => $child_id === 0 ? null : $child_id,
            'name'         => trim($request->request->get('name', $user ? $user->getFirstName() : $name)),
            'surname' => trim($request->request->get('surname', $user ? $user->getLastName() : $firstName)),
            'phone' => preg_replace(
                '/^[78]/',
                '+7',
                    preg_replace(
                    '/[^+0-9]/',
                    '',
                    $request->request->get('phone', $user ? $user->getPhone() : $phone))
            ),
            'ref-code' => substr(trim($request->query->get('ref-code', $code)), 4),
            'email' => trim($request->request->filter('email', $user ? $user->getEmail() : $email, FILTER_VALIDATE_EMAIL)),
            'sum' => round(
                $request->query->filter('sum', null, FILTER_VALIDATE_FLOAT)
                    ?: $request->request->filter('sum', 300, FILTER_VALIDATE_FLOAT),
                2
            ),
            'recurent' => (bool) $request->request->get('recurent',  $request->query->get('recurent') ??  true),
            'agree' => $request->request->get('agree', 'false')
        ];
        if ($request->isMethod('post')) {
            $form_errors = $this->validate($form);

            if (0 === count($form_errors)) {
                if (0 !== (int) $form['ref-code']) $session->set('referral', (int) $form['ref-code']);

                $form['referral'] = $form['ref-code'] ?: $request->cookies->get('referral');
                $form['ref_code'] = substr(base64_encode(random_bytes(20)), 0, 16);

                $entityManager = $this->getDoctrine()->getManager();
                [$user, $new]  = $usersService->findOrCreateUser($form);

                if (null==$this->getUser() && !$new){
                    // return $this->redirectToRoute('app_login', ['inputEmail' => $form['email']]);
                    $auth_errors='Указанный номер телефона привязан к другой почте';
                    return $this->render('donate/main.twig', ['form' => $form, 'formErrors' => $form_errors, 'auth_errors' => $auth_errors]);
                }
                $req = new \App\Entity\Request();
                $req->setSum($form['sum'])
                    ->setRecurent($form['recurent'])
                    ->setUser($user)
                    ->setJson($form)
                    ->setOrder_id('');
                $entityManager->persist($req);
                $entityManager->flush();
                // For Uniteller
                return $this->render(
                    'donate/paymentForm.twig',
                    [
                        'fields' => $unitellerService->getFromData($req, $request->request->get('EMoneyType', '0')),
                        'pm' => ['type' => $form['payment-type']]
                    ]
                );
            }
        }

        return $this->render('donate/main.twig', ['form' => $form, 'formErrors' => $form_errors, 'auth_errors' => $auth_errors]);
    }

    /**
     * @param \App\Entity\Request $request
     * @return bool
     * @throws \Exception
     */
    private function referralHistory(\App\Entity\Request $request): bool
    {
        if ($request->getUser()->getReferrer() === null) return false;
        $this->getDoctrine()->getManager()->persist(
            (new \App\Entity\ReferralHistory())
                ->setRequest($request)
                ->setSum($request->getSum() * self::REF_RATE)
                ->setUser($request->getUser())
                ->setDonator($request->getUser())
        );
        return true;
    }

    /**
     * @param \App\Entity\Request $request
     * @return bool
     * @throws \Exception
     */
    protected function childHistory(\App\Entity\Request $request): bool
    {
        $this->getDoctrine()->getManager()->persist(
            (new \App\Entity\ChildHistory())
                ->setSum($request->getSum())
                ->setChild($request->getChild())
                ->setDonator($request->getUser())
        );
        return true;
    }

    /**
     * @param array $data
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     */
    private function validate(array $data)
    {
        return Validation::createValidator()->validate(
            $data,
            new Assert\Collection([
                'payment-type' => new Assert\Choice(['visa', 'requisite-services', 'sms', 'eq']),
                'EMoneyType' => new Assert\Choice(['1', '13', '29', '0']),
                'ref-code' => new Assert\Length(['min' => 0, 'max' => 14]),
                'child_id' => new Assert\GreaterThan(['value' => 0]),
                'name' => new Assert\Length(['min' => 0, 'max' => 128]),
                'surname' => new Assert\Length(['min' => 0, 'max' => 128]),
                'phone' => [new Assert\NotBlank(), new Assert\Regex(['pattern' => '/^\+?\d{10,13}$/i'])],
                'email' => [new Assert\NotBlank(), new Assert\Email()],
                'sum' => new Assert\Range(['min' => 50, 'max' => 1000000]),
                'recurent' => new Assert\Type(['type' => 'boolean']),
                'agree' => new Assert\EqualTo('true')
            ])
        );
    }

    /**
     * @param Request                  $request
     * @param EventDispatcherInterface $dispatcher
     * @return Response
     * @throws \Exception
     */
    public function sendReminder(Request $request, EventDispatcherInterface $dispatcher) {
        $email = $request->request->get('email');
        $name = $request->request->get('name');
        $date = new DateTime($request->request->get('date'));

        $match_date = new DateTime($timestamp);
        $interval = $date->diff($match_date);

        $today = false;

        if ($interval->days == 0)
            $today = true;

        $lastName = $request->request->get('lastName');
        $phone = $request->request->get('phone');
        $code = null;

        $doctrine = $this->getDoctrine();
        $user = $doctrine->getRepository(User::class)->findOneBy([
            'email' => $email
        ]);

        if (null !== $user->getRefCode())
            $code = $user->getRefCode();
        else {
            $code = substr(md5(random_bytes(20)), 0, 16);
            $user->setRefCode($code);
            $doctrine->getManager()->persist($user);
            $doctrine->getManager()->flush();
        }

        if (!isset($email) || !isset($name) || !isset($date))
            return new Response('false');

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $dispatcher->dispatch(new SendReminderEvent($email, $name, $date, $lastName, $phone, $code, $today), SendReminderEvent::NAME);
        return new Response('true');
    }
}


