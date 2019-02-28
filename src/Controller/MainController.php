<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use App\Service\UsersService;

class MainController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function main()
    {
        return $this->render('pages/main.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function contacts()
    {
        return $this->render('pages/contacts.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function docs()
    {
        return $this->render('pages/docs.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function help()
    {
        return $this->render('pages/help.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function partners()
    {
        return $this->render('pages/partners.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function reports()
    {
        return $this->render('pages/reports.twig');
    }

    /**
     * @param Request                      $request
     * @param UsersService                 $usersService
     * @param SessionInterface             $session
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function donate(Request $request, UsersService $usersService)
    {
        $form_errors = [];
        $form = [
            'child_id' => trim($request->request->filter('child_id', 0, FILTER_VALIDATE_INT)),
            'fullName' => trim($request->request->get('fullName', '')),
            'phone' => trim($request->request->get('phone', '')),
            'email' => trim($request->request->get('email', '')),
            'sum' => (int) $request->request->filter('sum', 100, FILTER_VALIDATE_INT),
            'sumOther' => (int) $request->request->filter('sumOther', '', FILTER_VALIDATE_INT),
            'recurent' => (bool) $request->request->get('recurent', true),
            'agree' => (bool) $request->request->get('agree', true),
        ];

        if ($request->isMethod('post')) {
            $form_errors = $this->validate($form);

            if (!count($form_errors)) {

                $user = $usersService->findOrCreateUser($form);

                $req = new \App\Entity\Request();
                $req->setSum($form['sum'])
                    ->setRecurent($form['recurent'] ?? 'false')
                    ->setUserID($user->getUser());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($req);
                $entityManager->flush();

                $fields = [
                    'Shop_IDP' => 4996,
                    'Order_IDP' => $req->getId(),
                    'Subtotal_P' => $req->getSum(),
                    'MeanType' => '',
                    'EMoneyType'=> '',
                    'Lifetime' => 3600,
                    'Customer_IDP' => $req->getUser(),
                    'Card_IDP' => '', // Будем ли мы карты регистрировать?? Для реккурентных платжей они нужны?
                    'IData' => '',
                    'PT_Code' => '',
                    'password' => 'IzOb37ygmN9xAUdKFtLcVt82x2ir1ycGSXkTch03dblOPsLOGAyADKHC3WWVfcXNAOwLdxb2LaWa4vWH'
                ];

                $fields['Signature'] = $this->getSignature($fields);

                return $this->render('pages/paymentForm.twig', ['fields' => $fields]);
            }
        }

        return $this->render('pages/donate.twig', ['form' => $form, 'formErrors' => $form_errors]);
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

    private function getSignature(array $data) {
        return strtoupper(
            md5(
                md5($data['Shop_IDP']) . "&" .
                md5($data['Order_IDP']) . "&" .
                md5($data['Subtotal_P']) . "&" .
                md5($data['MeanType']) . "&" .
                md5($data['EMoneyType']) . "&" .
                md5($data['Lifetime']) . "&" .
                md5($data['Customer_IDP']) . "&" .
                //md5($data['Card_IDP']) . "&" .
                md5($data['password'])
            )
        );
    }
}
