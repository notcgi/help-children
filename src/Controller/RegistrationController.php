<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationController extends AbstractController
{
    public function template()
    {
        return $this->render('child/registration.twig');
    }

    public function index(Request $request)
    {
        // creates a task and gives it some dummy data for this example
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add(
                'surname',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add(
                'age',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank()
                    ],
                ]
            )
            ->add(
                'phone',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Email(),
                    ],
                ]
            )
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Поля пароля должны совпадать.',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'required' => true,
                    'first_options'  => ['label' => 'Введите пароль'],
                    'second_options' => ['label' => 'Повторите пароль'],
                    'constraints' => [
                        new NotBlank(),
                        new Length(['min' => 6, 'max' => 32]),
                    ],
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Зарегистрироваться',
                    'attr' => ['class' => 'btn btn--big btn-dark registration-form-submit'],
                ]
            )
            ->getForm();

        $form->handleRequest($request);

        /*if ($form->isSubmitted()) {
            var_dump($form->getErrors(true, true)[0]->getMessage());
        }*/

        return $this->render(
            'child/registration.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
