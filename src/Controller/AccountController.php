<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ReferralHistoryRepository;
use App\Repository\RequestRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     */
    public function main()
    {
        return $this->render('account/main.twig');
    }

    /**
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function myAccount(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $userID = $this->getUser();

        $userData = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($userID);

        if (!$userData) {
            throw $this->createNotFoundException(
                'Нет пользователя с id '.$userID
            );
        }
        $form = [
            'firstName' => trim($request->request->get('firstName', '')),
            'lastName' => trim($request->request->get('lastName', '')),
            'phone' => trim($request->request->get('phone', '')),
            'email' => trim($request->request->filter('email', '', FILTER_VALIDATE_EMAIL)),
            'oldPassword' => trim($request->request->filter('oldPassword', '')),
            'password' => trim($request->request->filter('Password', '')),
            'retypePassword' => trim($request->request->filter('retypePassword', '')),
        ];

        if ($request->isMethod('post')) {
            $form_errors = $this->validate($form);
            if($form['password'] != $form['retypePassword']){
                //$form_errors['Пароли не совпадают'];
            }
            if($form_errors === 0){
                if(!$encoder->isPasswordValid($userID, $form['oldPassword'])){
                    //$form_errors['passwordIsNotValid'] = 'Вы ввели неверный пароль';
                    return $this->render('account/myAccount.twig', ['userData'=>$userData, 'formErrors' => $form_errors]);
                }

                $encoded = $encoder->encodePassword($userID, $form['password']);

                $userData->setFirstName($form['firstName'])
                    ->setLastName($form['lastName'])
                    ->setPhone($form['phone'])
                    ->setEmail($form['email'])
                    ->setPassword($encoded);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($userData);
                $entityManager->flush();
            }
        }

        return $this->render('account/myAccount.twig', ['userData'=>$userData, 'formErrors' => $form_errors]);
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
                    'firstName' => [new Assert\NotBlank(), new Assert\Length(['min' => 3, 'max' => 256])],
                    'lastName' => [new Assert\NotBlank(), new Assert\Length(['min' => 3, 'max' => 256])],
                    'phone' => [new Assert\Regex(['pattern' => '/^\+?\d{10,13}$/i'])],
                    'email' => [new Assert\NotBlank(), new Assert\Email()],
                    'oldPassword' => [new Assert\NotBlank()],
                    'password' => [new Assert\NotBlank()],
                    'retypePassword' => [new Assert\NotBlank()],
                ]
            )
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     */
    public function history()
    {
        /** @var RequestRepository $repository */
        $repository = $this->getDoctrine()->getRepository(\App\Entity\Request::class);

        return $this->render('account/history.twig', [
            'entities' => $repository->findRequestsDonateWithUser()
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     */
    public function referrals()
    {
        /** @var ReferralHistoryRepository $repository */
        $repository = $this->getDoctrine()->getRepository(\App\Entity\ReferralsHistory::class);

        return $this->render('account/referrals.twig', [
            'entities' => $repository->findReferralsWithUser()
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     */
    public function recurrent()
    {
        return $this->render('account/recurrent.twig');
    }
}
