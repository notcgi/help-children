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
            'Signature' => $request->request->get('Signature', ''),
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
            'Signature' => $request->request->get('Signature', ''),
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
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \RuntimeException
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

        $entityManager->persist($req);
        $entityManager->flush();

        return new Response('OK');
    }

    /**
     * @param Request          $request
     * @param UsersService     $usersService
     * @param UnitellerService $unitellerService
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \RuntimeException
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
        UnitellerService $unitellerService
    ) {
        $form_errors = [];
        $form = [
            'payment-type' => trim($request->request->get('payment-type', 'visa')),
            'child_id' => trim($request->request->filter('child_id', null, FILTER_VALIDATE_INT)),
            'fullName' => trim($request->request->get('fullName', '')),
            'phone' => trim($request->request->get('phone', '')),
            'email' => trim($request->request->filter('email', '', FILTER_VALIDATE_EMAIL)),
            'sum' => (int) $request->request->filter('sum', 300, FILTER_VALIDATE_INT),
            'sumOther' => (int) $request->request->filter('sumOther', '', FILTER_VALIDATE_INT),
            'recurent' => (bool) $request->request->get('recurent', true),
            'agree' => (bool) $request->request->get('agree', true),
        ];

        if ($request->isMethod('post')) {
            $form_errors = $this->validate($form);

            if (0 === count($form_errors)) {
                $req = new \App\Entity\Request();
                $req->setSum(round($form['sum'], 2))
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

        // Add referral
        $refHistory = new \App\Entity\ReferralHistory();
        $refHistory->setSum($refSum)
            ->setUser($user);
        $this->getDoctrine()->getManager()->persist($refHistory);

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
            new Assert\Collection(
                [
                    'child_id' => new Assert\GreaterThan(['value' => 0]),
                    'fullName' => [new Assert\NotBlank(), new Assert\Length(['min' => 8, 'max' => 256])],
                    'phone' => [new Assert\NotBlank(), new Assert\Regex(['pattern' => '/^\+?\d{10,13}$/i'])],
                    'email' => [new Assert\NotBlank(), new Assert\Email()],
                    'sum' => new Assert\Choice([300, 500]),
                    'sumOther' => new Assert\Range(['min' => 0, 'max' => 1000000]),
                    'recurent' => new Assert\Type(['type' => 'boolean']),
                    'agree' => new Assert\IsTrue(),
                ]
            )
        );
    }
}
