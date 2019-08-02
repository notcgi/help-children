<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\RegistrationEvent;
use App\Event\RequestSuccessEvent;
use App\Event\SendReminderEvent;
use App\Service\UnitellerService;
use App\Service\UsersService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class DonateController extends AbstractController
{
    const REF_RATE = .06;

    /**
     * @param Request          $request
     * @param UnitellerService $unitellerService
     *
     * @return Response
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \Exception
     */
    public function ok(Request $request)
    {
        try {
            $form = $request->request->all();
        } catch (\JsonException $e) {
            return new Response('invalid data', 400);
        }
        file_put_contents(dirname(__DIR__)."/../var/logs/ok.log", date("d.m.Y H:i:s")."; ".print_r($request->request->all(), true)."\n", FILE_APPEND);# FILE_APPEND | LOCK_EX
        file_put_contents(dirname(__DIR__)."/../var/logs/ok.log", date("d.m.Y H:i:s")."; ".print_r($request->query->all(), true)."\n", FILE_APPEND);# FILE_APPEND | LOCK_EX
        /*if ($form['Signature'] != $unitellerService->signatureVerification($form)) {
            return $this->render('account/history.twig');
        }*/

        $entityManager = $this->getDoctrine()->getManager();

        $req = $entityManager->getRepository(\App\Entity\Request::class)->find($form['Order_ID']);

        if (!$req) {
            return new Response('order not found', 404);
        }

        if ($form['Status'] == 'Completed') {
            $req->setStatus(2);
            $this->refHistory($req->getUser(), $req->getSum());

            // Add child history
            $childHistory = new \App\Entity\ChildHistory();
            $childHistory->setSum($req->getSum())
                ->setChild($req->getChild());
            $entityManager->persist($childHistory);
        } elseif ($form['Status'] != '') {
            $req->setStatus(1);
        }

        $entityManager->persist($req);
        $entityManager->flush();

        #help https://symfony.com/doc/current/components/http_foundation.html
        return new Response(json_encode(["code"=>'0']), Response::HTTP_OK, ['content-type' => 'text/html']);
    }

    /**
     * @param Request          $request
     * @param UnitellerService $unitellerService
     *
     * @return Response
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \Exception
     */
    public function no(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('account/history.twig');
      
        try {
            $form = $request->request->all();
        } catch (\JsonException $e) {
            return new Response('invalid data', 400);
        }
       /*
        $form = [
            'Order_ID' => (int) $request->request->filter('Order_ID', null, FILTER_VALIDATE_INT),
            // Status может принимать основные значения: authorized, paid, canceled, waiting
            'Status' => $request->request->get('Status', ''),
            'Signature' => $request->request->get('Signature', '')
        ];*/

        /*if ($form['Signature'] != $unitellerService->signatureVerification($form)) {
            return $this->render('account/history.twig');
        }*/

        $entityManager = $this->getDoctrine()->getManager();
        $req = $entityManager->getRepository(\App\Entity\Request::class)->find($form['Order_ID']);

        if (!$req) {
            return new Response('', 404);
        }

        if ($form['Status'] == 'paid') {
            $req->setStatus(2);
            $this->refHistory($req->getUser(), $req->getSum());

            // Add child history
            $childHistory = new \App\Entity\ChildHistory();
            $childHistory->setSum($req->getSum())
                ->setChild($req->getChild());
            $entityManager->persist($childHistory);
        } elseif ($form['Status'] != '') {// canceled
            $req->setStatus(1);
        }

        $entityManager->persist($req);
        $entityManager->flush();

        #return new Response('OK');
        return new Response(json_encode(["code"=>'0']), Response::HTTP_OK, ['content-type' => 'text/html']);
    }

    /**
     * 4242424242424242
     * CLOUDPAYMENTS TEST
     *
     * @param Request                  $request
     * @param UnitellerService         $unitellerService
     * @param EventDispatcherInterface $dispatcher
     *
     * @return Response
     * @throws \Exception
     */
    public function status(Request $request, UnitellerService $unitellerService, EventDispatcherInterface $dispatcher)
    {
        try {
            $form = $request->request->all();
        } catch (\JsonException $e) {
            return new Response('invalid data', 400);
        }

        /*if (!$unitellerService->validateStatusSignature($form)) {
            return new Response('', 400);
        }*/

        $entityManager = $this->getDoctrine()->getManager();
        /** @var \App\Entity\Request $req */
        $req = $entityManager->getRepository(\App\Entity\Request::class)->find($form['InvoiceId']);

        if (!$req) {
            return new Response('', 404);
        }

        file_put_contents(dirname(__DIR__)."/../var/logs/status.log", date("d.m.Y H:i:s")."; POST ".print_r($_POST, true). "\n GET ".print_r($_GET, true)."\n form".print_r($form, true)."\n", FILE_APPEND);

        switch ($form['Status']) {
            #case 'paid':
            #case 'authorized':
            case 'Completed':
                $req->setStatus(2);
                $req->setTransactionId($form['TransactionId']); #avtorkoda
                $req->setJson(json_encode($form));              #avtorkoda
                $dispatcher->dispatch(RequestSuccessEvent::NAME, new RequestSuccessEvent($req));

                if ($req -> isRecurent()) {//оформление подписки

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,"https://api.cloudpayments.ru/subscriptions/create");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_USERPWD, "pk_51de50fd3991dbf5b3610e65935d1:ecbe13569e824fa22e85774015784592");
                    curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, "token=".$form['Token']."&accountId=".$form['AccountId']."&description=Ежемесячня подписка на сервис ПомогитеДетям.рф&email=".$form['Email']."&amount=".$form['Amount']."&currency=RUB&requireConfirmation=false&startDate=".gmdate("Y-m-d\TH:i:s\Z", strtotime("+1 month"))."&interval=Month&period=1");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $server_output = curl_exec ($ch);

                    $req->setSubscriptionsId('abc123');

                    file_put_contents(dirname(__DIR__)."/../var/logs/recurent.log", date("d.m.Y H:i:s")."; POST ".print_r($_POST, true). "\n GET ".print_r($_GET, true)."\n form".print_r($server_output, true)."\n", FILE_APPEND);

                    curl_close ($ch);
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

    /**
     * @param Request                   $request
     * @param UsersService              $usersService
     * @param UnitellerService          $unitellerService
     * @param SessionInterface          $session
     * @param EventDispatcherInterface  $dispatcher
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator    $authenticator
     *
     * @return Response
     * @throws \LogicException
     * @throws \RuntimeException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
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
        if (!$this->isGranted('ROLE_USER')) {
            $email = $request->query->get('email');
            $code = $request->query->get('code');

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
        $child_id = (int) $request->request->filter('child_id', null, FILTER_VALIDATE_INT);
        $form = [
            'payment-type' => trim($request->request->get('payment-type', 'visa')),
            'child_id' => $child_id === 0 ? null : $child_id,
            'name' => trim($request->request->get('name', $user ? $user->getFirstName() : '')),
            'surname' => trim($request->request->get('surname', $user ? $user->getLastName() : '')),
            'phone' => preg_replace(
                '/[^+0-9]/',
                '',
                $request->request->get('phone', $user ? $user->getPhone() : '')
            ),
            'ref-code' => substr(trim($request->query->get('ref-code', '')), 4),
            'email' => trim($request->request->filter('email', $user ? $user->getEmail() : '', FILTER_VALIDATE_EMAIL)),
            'sum' => round(
                $request->query->filter('sum', null, FILTER_VALIDATE_FLOAT)
                    ?: $request->request->filter('sum', 500, FILTER_VALIDATE_FLOAT),
                2
            ),
            'recurent' => (bool) $request->request->get('recurent', true),
            'agree' => $request->request->get('agree', 'false')
        ];

        if ($request->isMethod('post')) {
            $form_errors = $this->validate($form);

            if (0 === count($form_errors)) {
                if (0 !== (int) $form['ref-code']) {
                    $session->set('referral', (int) $form['ref-code']);
                }

                $form['referral'] = $form['ref-code'] ?: $request->cookies->get('referral');
                $form['ref_code'] = substr(base64_encode(random_bytes(20)), 0, 16);
                $req = new \App\Entity\Request();
                $req->setSum($form['sum'])
                    ->setRecurent($form['recurent'])
                    ->setUser($usersService->findOrCreateUser($form));

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($req);
                $entityManager->flush();                

                return $this->render('donate/paymentForm.twig', ['fields' => $unitellerService->getFromData($req)]);
            }
        }

        return $this->render('donate/main.twig', ['form' => $form, 'formErrors' => $form_errors]);
    }

    /**
     * @param User  $user
     * @param float $sum
     *
     * @return bool
     * @throws \LogicException
     * @throws \Exception
     */
    private function refHistory(User $user, float $sum): bool
    {
        if ($user->getReferrer() === null) {
            return false;
        }

        $refSum = $sum * self::REF_RATE;

        // Add referral;
        $this->getDoctrine()
            ->getManager()
            ->persist(
                (new \App\Entity\ReferralHistory())
                    ->setSum($refSum)
                    ->setUser($user)
            );

        return true;
    }

    /**
     * @param array $data
     *
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
                'payment-type' => new Assert\Choice(['visa', 'requisite-services']),
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

    public function sendReminder(Request $request, EventDispatcherInterface $dispatcher) {
        $email = $request->request->get('email');
        $name = $request->request->get('name');
        $date = $request->request->get('date');

        if (!isset($email) || !isset($name) || !isset($date))
            return new Response('false');

        $dispatcher->dispatch(new SendReminderEvent($email, $name, $date), SendReminderEvent::NAME);
        return new Response('true');
    }
}
