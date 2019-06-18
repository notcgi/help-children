<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler    $guardHandler
     * @param LoginFormAuthenticator       $authenticator
     *
     * @return Response
     *
     * @throws \Exception
     * @throws \LogicException
     * @throws \OutOfBoundsException
     * @throws \Symfony\Component\Form\Exception\LogicException
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        if ($request->isMethod('POST')) {
            $request->request->set('phone', preg_replace(
                '/[^+0-9]/',
                '',
                $request->request->get('phone', '')
            ));
        }

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $doctrine = $this->getDoctrine();
            $valid = false;

            if ($form->isValid()) {
                /** @var User $old_user */
                $old_user = $doctrine->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
                $valid = true;

                if ($old_user) {
                    if (!$old_user->getPass()) {
                        $user = $old_user;
                    } else {
                        $valid = false;
                        $form->addError(new FormError('E-mail уже занят'));
                    }
                }
            }

            if ($valid) {
                // encode the plain password
                $user->setPass(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );

                $entityManager = $doctrine->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                // do anything else you need here, like send an email
                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main' // firewall name in security.yaml
                );
            }
        }

        return $this->render(
            'auth/registration.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
