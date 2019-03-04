<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UnitellerService;
use App\Service\UsersService;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class DonateController extends AbstractController
{

    const REF_RATE = 6;

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     */
    public function ok()
    {
        return $this->render('account/main.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     */
    public function no()
    {
        return $this->render('account/myAccount.twig');
    }

    /**
     * @param Request          $request
     * @param UnitellerService $unitellerService
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function status(Request $request, UnitellerService $unitellerService)
    {
        if ($request->isMethod('post')) {
            $form = [
                'Order_ID' => $request->request->filter('Order_ID', null, FILTER_VALIDATE_INT),
                // Status может принимать основные значения: authorized, paid, canceled, waiting
                'Status' => $request->request->get('Status', ''),
                'Signature' => $request->request->get('Signature', '')
            ];

            $checkSignature = $unitellerService->signatureVerification($form);
            if ($form['Signature'] == $checkSignature) {

                if ($form['Status'] == 'paid') {
                    $entityManager = $this->getDoctrine()->getManager();
                    $req = $entityManager->getRepository(\App\Entity\Request::class)->find($form['Order_ID']);

                    if ($req) {
                        $req->setStatus(2);
                        $entityManager->flush();

                        $this->refHistory($req->getUser(), $req->getSum());

                        // Add child history
                        $childHistory = new \App\Entity\ChildHistory();
                        $childHistory->setSum($req->getSum())
                            ->setChildID($req->getChild());

                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($childHistory);
                        $entityManager->flush();
                    }
                }

                if ($form['Status'] != '') {
                    $entityManager = $this->getDoctrine()->getManager();
                    $req = $entityManager->getRepository(\App\Entity\Request::class)->find($form['Order_ID']);
                    if ($req) {
                        $req->setStatus(1);
                        $entityManager->flush();
                    }
                }
            }
        }

        return $this->render('account/history.twig');
    }

    /**
     * @param int   $userID
     * @param float $sum
     *
     * @throws \Exception
     */
    private function refHistory(int $userID, float $sum)
    {
        $users = $this->getDoctrine()->getRepository(User::class);

        $user = $users->find($userID);

        if ($user && $user->getReferrer() != null) {
            $refSum = $sum * self::REF_RATE / 100;

            // Add referral
            $refHistory = new \App\Entity\ReferralsHistory();
            $refHistory->setSum($refSum)
                ->setUser($user->getId());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($refHistory);
            $entityManager->flush();
        }
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
    public function main(Request $request, UsersService $usersService, UnitellerService $unitellerService)
    {
        $form_errors = [];
        $form = [
            'child_id' => trim($request->request->filter('child_id', null, FILTER_VALIDATE_INT)),
            'fullName' => trim($request->request->get('fullName', '')),
            'phone' => trim($request->request->get('phone', '')),
            'email' => trim($request->request->get('email', '')),
            'sum' => (int)$request->request->filter('sum', 100, FILTER_VALIDATE_INT),
            'sumOther' => (int)$request->request->filter('sumOther', '', FILTER_VALIDATE_INT),
            'recurent' => (bool)$request->request->get('recurent', false),
            'agree' => (bool)$request->request->get('agree', true),
        ];

        if ($request->isMethod('post')) {
            $form_errors = $this->validate($form);

            if (!count($form_errors)) {
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
                    'fullName' => new Assert\Length(['min' => 8, 'max' => 256]),
                    'phone' => new Assert\Regex(['pattern' => '/^\+?\d{10,13}$/i']),
                    'email' => new Assert\Email(),
                    'sum' => new Assert\Choice([100, 500]),
                    'sumOther' => new Assert\Range(['min' => 0, 'max' => 1000000]),
                    'recurent' => new Assert\Type(['type' => 'boolean']),
                    'agree' => new Assert\IsTrue(),
                ]
            )
        );
    }
}
