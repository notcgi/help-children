<?php

namespace App\Controller;

use App\Service\UnitellerService;
use App\Service\UsersService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class DonateController extends AbstractController
{
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
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     */
    public function status()
    {
        return $this->render('account/history.twig');
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
            'sum' => (int) $request->request->filter('sum', 100, FILTER_VALIDATE_INT),
            'sumOther' => (int) $request->request->filter('sumOther', '', FILTER_VALIDATE_INT),
            'recurent' => (bool) $request->request->get('recurent', false),
            'agree' => (bool) $request->request->get('agree', true),
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
