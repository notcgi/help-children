<?php

namespace App\Controller;

use App\Entity\RecurringPayment;
use App\Entity\User;
use App\Repository\RequestRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class AccountController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @throws \LogicException
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     */
    public function myAccount(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $form = [
            'firstName' => trim($request->request->get('firstName', '')),
            'lastName' => trim($request->request->get('lastName', '')),
            'phone' => trim($request->request->get('phone', '')),
            'email' => trim($request->request->filter('email', '', FILTER_VALIDATE_EMAIL)),
            'oldPassword' => trim($request->request->filter('oldPassword', '')),
            'password' => trim($request->request->filter('Password', '')),
            'retypePassword' => trim($request->request->filter('retypePassword', ''))
        ];
        $form_errors = [];

        if ($request->isMethod('post')) {
            $form_errors = $this->validate($form);

            if ($form_errors->count() === 0) {
                $user->setFirstName($form['firstName'])
                    ->setLastName($form['lastName'])
                    ->setPhone($form['phone'])
                    ->setEmail($form['email'])
                    ->setPassword($encoder->encodePassword($user, $form['password']));

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            }
        }

        return $this->render('account/myAccount.twig', ['userData' => $user, 'formErrors' => $form_errors]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function history()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var RequestRepository $repository */
        $repository = $this->getDoctrine()->getRepository(\App\Entity\Request::class);

        return $this->render(
            'account/history.twig',
            [
                'entities' => $repository->findRequestsDonateWithUser($this->getUser())
            ]
        );
    }

    /**
     * @param Request $request
     * @param UrlGeneratorInterface $generator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function referrals(Request $request, UrlGeneratorInterface $generator)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var UserRepository $repository */
        $repository = $this->getDoctrine()->getRepository(User::class);

        return $this->render(
            'account/referrals.twig',
            [
                'entities' => $repository->findReferralsWithHistory($this->getUser()),
                'referral_url' => $request->getScheme().'://'.idn_to_utf8($request->getHost()).$generator->generate('referral', ['id' => $this->getUser()->getId()])
            ]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \UnexpectedValueException
     */
    public function recurrent()
    {
        return $this->render(
            'account/recurrent.twig',
            [
                'payments' => $this->getDoctrine()
                    ->getRepository(RecurringPayment::class)
                    ->findBy([
                        'user' => $this->getUser()
                    ])
            ]
        );
    }

    /**
     * @param int                   $id
     * @param Request               $request
     * @param UrlGeneratorInterface $generator
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \LogicException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     */
    public function recurrent_remove(int $id, Request $request, UrlGeneratorInterface $generator)
    {
        if (!$this->isCsrfTokenValid('delete-item', $request->request->get('token'))) {
            return $this->redirect($generator->generate('account_recurrent'));
        }

        $doctrine = $this->getDoctrine();
        /** @var RecurringPayment $payment */
        $payment = $doctrine->getRepository(RecurringPayment::class)->find($id);

        if (!$payment || $payment->getUser()->getId() !== $this->getUser()->getId()) {
            throw $this->createNotFoundException(
                'Нет платежа с id '.$id
            );
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($payment);
        $entityManager->flush();

        return $this->redirect($generator->generate('account_recurrent'));
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
                'firstName' => [new Assert\NotBlank(), new Assert\Length(['min' => 3, 'max' => 256])],
                'lastName' => [new Assert\NotBlank(), new Assert\Length(['min' => 3, 'max' => 256])],
                'phone' => new Assert\Regex(['pattern' => '/^\+?\d{10,13}$/i']),
                'email' => [new Assert\NotBlank(), new Assert\Email()],
                'oldPassword' => [new Assert\NotBlank(), new SecurityAssert\UserPassword()],
                'password' => [new Assert\NotBlank(), new Assert\EqualTo(['propertyPath' => 'retypePassword'])],
                'retypePassword' => [new Assert\NotBlank()]
            ])
        );
    }
}
