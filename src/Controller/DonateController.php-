<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\RequestSuccessEvent;
use App\Service\UnitellerService;
use App\Service\UsersService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    public function ok(Request $request, UnitellerService $unitellerService)
    {
        $form = [
            'Order_ID' => (int) $request->request->filter('Order_ID', null, FILTER_VALIDATE_INT),
            // Status может принимать основные значения: authorized, paid, canceled, waiting
            'Status' => $request->request->get('Status', ''),
            'Signature' => $request->request->get('Signature', '')
        ];

        if ($form['Signature'] != $unitellerService->signatureVerification($form)) {
            return $this->render('account/history.twig');
        }

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
        } elseif ($form['Status'] != '') {
            $req->setStatus(1);
        }

        $entityManager->persist($req);
        $entityManager->flush();

        return new Response('OK');
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
    public function no(Request $request, UnitellerService $unitellerService)
    {
        $form = [
            'Order_ID' => (int) $request->request->filter('Order_ID', null, FILTER_VALIDATE_INT),
            // Status может принимать основные значения: authorized, paid, canceled, waiting
            'Status' => $request->request->get('Status', ''),
            'Signature' => $request->request->get('Signature', '')
        ];

        if ($form['Signature'] != $unitellerService->signatureVerification($form)) {
            return $this->render('account/history.twig');
        }

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
        } elseif ($form['Status'] != '') {
            $req->setStatus(1);
        }

        $entityManager->persist($req);
        $entityManager->flush();

        return new Response('OK');
    }

    /**
     * 4000000000002487
     * UNITELLER TEST
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
            $form = json_decode($request->getContent(), true, 2, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return new Response('invalid json', 400);
        }

        if (!$unitellerService->validateStatusSignature($form)) {
            return new Response('', 400);
        }

        $entityManager = $this->getDoctrine()->getManager();
        /** @var \App\Entity\Request $req */
        $req = $entityManager->getRepository(\App\Entity\Request::class)->find($form['Order_ID']);

        if (!$req) {
            return new Response('', 404);
        }

        switch ($form['Status']) {
            case 'paid':
            case 'authorized':
                $req->setStatus(2);
                $dispatcher->dispatch(RequestSuccessEvent::NAME, new RequestSuccessEvent($req));
                break;
            case 'canceled':
                $req->setStatus(1);
        }

        $entityManager->persist($req->setUpdatedAt(new \DateTime()));
        $entityManager->flush();

        return new Response('OK');
    }

    /**
     * @param Request          $request
     * @param UsersService     $usersService
     * @param UnitellerService $unitellerService
     * @param SessionInterface $session
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
        SessionInterface $session
    ) {
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
            'ref-code' => substr(trim($request->request->get('ref-code', '')), 4),
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
                $form['refCode'] = substr(base64_encode(random_bytes(20)), 0, 16);
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
}
