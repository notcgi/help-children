<?php

namespace App\Controller;

use App\Entity\RecurringPayment;
use App\Entity\User;
use App\Event\RecurringPaymentRemove;
use App\Repository\RequestRepository;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

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
     * @param UrlGeneratorInterface        $generator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     * @throws \Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     */
    public function myAccount(
        Request $request,
        UserPasswordEncoderInterface $encoder,
        UrlGeneratorInterface $generator
    ) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();            
        $form = [
            'firstName' => trim($request->request->get('firstName', '')),
            'lastName' => trim($request->request->get('lastName', '')),
            'age' => $request->request->get('age', ''),
            'phone' => preg_replace(
                '/[^+0-9]/',
                '',
                $request->request->get('phone', '')
            ),
            'email' => trim($request->request->filter('email', '', FILTER_VALIDATE_EMAIL)),
            'oldPassword' => trim($request->request->filter('oldPassword', '')),
            'password' => trim($request->request->filter('password', '')),
            'retypePassword' => trim($request->request->filter('retypePassword', ''))
        ];
        
        $form_errors = [];
        $errors = null;

        if ($request->isMethod('post')) {            
            $form_errors = $this->validate($form);
            if (!$encoder->isPasswordValid($user, $form['oldPassword']))
                $errors[] = 'Неверный текущий пароль';

            if ($form_errors->count() === 0 && $encoder->isPasswordValid($user, $form['oldPassword'])) {
                $user->setFirstName($form['firstName'])
                    ->setLastName($form['lastName'])
                    ->setAge($form['age'])
                    ->setPhone($form['phone'])
                    ->setEmail($form['email']);
                
                $errors[] = 'Данные сохранены';
            
                if (!empty($form['password'])) {
                    if ($form['password'] == $form['retypePassword']) {
                        $user->setPass($encoder->encodePassword($user, $form['password']));                        
                        $errors[] = 'Пароль успешно изменён';
                    }
                    else
                        $errors[] = 'Новые пароли не совпадают';
                }

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            }
        }

        return $this->render('account/myAccount.twig', [
            'userData' => $user,
            'errors' => $errors,
            'formErrors' => $form_errors,
            'referral_url' => $request->getScheme()
                .'://'
                .idn_to_utf8($request->getHost())
                .$generator->generate('referral', ['id' => $this->getUser()->getId()])
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
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
     * @param Request               $request
     * @param UrlGeneratorInterface $generator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function referrals(Request $request, UrlGeneratorInterface $generator)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var UserRepository $repository */
        $repository = $this->getDoctrine()->getRepository(User::class);

        return $this->render(
            'account/referrals.twig',
            [
                'entities' => $repository->findReferralsWithSum($this->getUser()),
                'referral_url' => $request->getScheme()
                    .'://'
                    .idn_to_utf8($request->getHost())
                    .$generator->generate('referral', ['id' => $this->getUser()->getId()])
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
     * @param int                      $id
     * @param Request                  $request
     * @param UrlGeneratorInterface    $generator
     * @param EventDispatcherInterface $dispatcher
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \LogicException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     */
    public function recurrent_remove(
        int $id,
        Request $request,
        UrlGeneratorInterface $generator,
        EventDispatcherInterface $dispatcher
    ) {
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

        $entityManager = $this->getDoctrine()->getManager();
        /** @var \App\Entity\Request $req */
        $req = $entityManager->getRepository(\App\Entity\Request::class)->find($id);

        $SubscriptionsId = $req->getSubscriptionsId();

        if (trim($SubscriptionsId)) {
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL,"https://api.cloudpayments.ru/subscriptions/cancel");
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_USERPWD, "pk_51de50fd3991dbf5b3610e65935d1:ecbe13569e824fa22e85774015784592");
          curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
          curl_setopt($ch, CURLOPT_POSTFIELDS, "Id=".$SubscriptionsId);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $server_output = curl_exec ($ch);

          curl_close ($ch);
          $json = json_decode($server_output);
          if ($json->Success) {
            // удаление оплаты на сайте (в базе)
              $entityManager = $doctrine->getManager();
              $entityManager->remove($payment);

              $dispatcher->dispatch(RecurringPaymentRemove::NAME, new RecurringPaymentRemove($payment));
              $entityManager->flush();
          }
        }

        return $this->redirect($generator->generate('account_recurrent'));
    }

    /**
     * @param array $data
     *
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     *
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
                'age' => [],
                'phone' => new Assert\Regex(['pattern' => '/^\+?\d{10,13}$/i']),
                'email' => new Assert\NotBlank(),
                'oldPassword' => [new Assert\NotBlank(), new Assert\Length(['min' => 6, 'max' => 64])],
                'password' => [
                    new Assert\Length(['min' => 0, 'max' => 64]),
                    new Assert\EqualTo(['propertyPath' => 'retypePassword'])
                ],
                'retypePassword' => new Assert\Length(['min' => 0, 'max' => 64])
            ])
        );
    }
}
