<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PanelController extends AbstractController
{
    public function auth(Request $request)
    {
        // creates a task and gives it some dummy data for this example
        $user = new User();

        $form = $this->createFormBuilder($user)
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
                'pass',
                PasswordType::class,
                [
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
                    'label' => 'Submit',
                    'attr' => ['class' => 'btn btn-primary'],
                ]
            )
            ->getForm();

        $form->handleRequest($request);

        /*if ($form->isSubmitted()) {
            var_dump($form->getErrors(true, true)[0]->getMessage());
        }*/

        return $this->render(
            'panel/auth.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    public function main()
    {
        return $this->render(
            'panel/main.twig',
            ['users' => $this->getDoctrine()->getRepository(User::class)->findAll()]
        );
    }

    public function users()
    {
        return $this->render(
            'panel/users/users.twig',
            ['users' => $this->getDoctrine()->getRepository(User::class)->findAll()]
        );
    }

    public function requests()
    {
        return $this->render(
            'panel/requests.twig'
        );
    }

    public function payments()
    {
        return $this->render(
            'panel/payments.twig'
        );
    }
}
