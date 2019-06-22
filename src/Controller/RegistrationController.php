<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\RegistrationEvent;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class RegistrationController extends AbstractController
{
    /**
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler    $guardHandler
     * @param LoginFormAuthenticator       $authenticator
     * @param EventDispatcherInterface     $dispatcher
     *
     * @return Response
     *
     * @throws \LogicException
     * @throws \OutOfBoundsException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\RuntimeException
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator,
        EventDispatcherInterface $dispatcher
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        if ($request->isMethod('post')) {
            $regfrom = $request->request->get('registration_form');
            $regfrom['phone'] = preg_replace(
                '/[^+0-9]/',
                '',
                $regfrom['phone']
            );
            $request->request->set('registration_form', $regfrom);
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
                )->setRefCode(substr(base64_encode(random_bytes(20)), 0, 16));

                $entityManager = $doctrine->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $dispatcher->dispatch(new RegistrationEvent($user));

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

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \LogicException
     */
    public function confirmCode(Request $request)
    {
        $form = [
            'code' => $request->query->get('code'),
            'email' => $request->query->filter('email', FILTER_VALIDATE_EMAIL)
        ];

        $form_errors = $this->validate($form);

        if (0 === count($form_errors)) {
            $doctrine = $this->getDoctrine();

            /** @var User $user */
            $user = $doctrine->getRepository(User::class)->findOneBy([
                'refCode' => $form['code'],
                'email' => $form['email']
            ]);

            if ($user) {
                $doctrine->getManager()->persist($user->setRefCode(null));
                $doctrine->getManager()->flush();
                $this->addFlash('code_confirm', 'E-mail подтверждён');
            }
        }

        return $this->redirect('/');
    }

    /**
     * @param array $data
     *
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     */
    private function codeValidate(array $data)
    {
        return Validation::createValidator()->validate(
            $data,
            new Assert\Collection([
                'code' => new Assert\Length(['min' => 16, 'max' => 16]),
                'email' => [new Assert\NotBlank(), new Assert\Email()],
            ])
        );
    }
}
