<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\ResetPasswordEvent;
use App\Form\ResetPasswordFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     * @throws \LogicException
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted('ROLE_USER')) {
            return $this->redirect('/account');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $request->query->get('inputEmail') ?? $authenticationUtils->getLastUsername();

        return $this->render('auth/login.twig', ['email' => $lastUsername, 'error' => $error]);
    }

    /**
     * @param Request $request
     * @param EventDispatcherInterface $dispatcher
     * 
     *
     * @return Response
     * @throws \LogicException
     */
    public function recovery(Request $request, EventDispatcherInterface $dispatcher): Response
    {
        if ($this->isGranted('ROLE_USER')) {
            return $this->redirect('/account');
        }

        $error = null;

        if ($request->isMethod('post')) {
            $doctrine = $this->getDoctrine();
            $mail = $request->request->get('email');
            $user = $doctrine->getRepository(User::class)->findOneBy(['email' => $mail]);
            if ($user) {
                $error = 'На почту отправлены указания по восстановлению пароля.';
                
                $user->setPass(substr(hash('sha256', random_bytes(20)), 0, 90));
                $entityManager = $doctrine->getManager();
                $entityManager->persist($user);
                $entityManager->flush();  
                $dispatcher->dispatch(new ResetPasswordEvent($user), ResetPasswordEvent::NAME);
            }
            else {
                $error = 'Пользователя с таким почтовым адресом не найдено.';
            }
        }

        // get the login error if there is one
        // $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        //$lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/passwordRecovery.twig', ['error' => $error]);
    }

    /**
     * @param Request                      $request
     * @param AuthenticationUtils          $authenticationUtils
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response
     * @throws \LogicException
     */
    public function reset_password(Request $request, AuthenticationUtils $authenticationUtils,      UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if ($request->query->get("email", null) === null)
            return $this->redirect('/');

        $doctrine = $this->getDoctrine();
        $mail = $request->query->get('email');
        $old_user = $doctrine->getRepository(User::class)->findOneBy(['email' => $mail]);
        if (!$old_user) 
            return $this->redirect('/');

        $user = new User();
        $form = $this->createForm(ResetPasswordFormType::class, $user);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $token = $request->query->get("token", null);
            $resetToken = $old_user->getPass();

            if ($token != $resetToken || $token == null)
                return $this->redirect('/');

            $old_user->setPass(null);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($old_user);
            $entityManager->flush();  
        }             

        if ($form->isSubmitted()) {
            $doctrine = $this->getDoctrine();            

            if ($form->isValid()) {                
                // encode the plain password
                $old_user->setPass(
                    $passwordEncoder->encodePassword(
                        $old_user,
                        $form->get('password')->getData()
                    )
                );

                $entityManager = $doctrine->getManager();
                $entityManager->persist($old_user);
                $entityManager->flush();            

                return $this->redirect('/login');
            }
        }

        $title = 'Восстановление пароля';
        $description = 'Введите новый пароль';
        $value = 'Восстановить';
        
        return $this->render(
            'auth/resetPassword.twig',
            [
                'form' => $form->createView(),
                'title' => $title, 
                'description' => $description, 
                'value' => $value
            ]
        );
    }

    function check_email(Request $request) {
        $current_user = $this->getUser();
        $doctrine = $this->getDoctrine();
        $mail = $request->request->get('email');
        $phone = $request->request->get('phone');
        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => $mail]);
        if ($user) {
            if ($current_user && ($mail === $current_user->getEmail()))
                return new Response('same');
            return new Response('exist');
        }
        $old_user = $doctrine->getManager()->createQuery("SELECT u FROM App\\Entity\\User u WHERE JSON_VALUE(u.meta, '$.phone') = ". $phone)->getResult();
        if ($old_user) {
            return new Response('phone');
        }
        return new Response('free');
    }
}
