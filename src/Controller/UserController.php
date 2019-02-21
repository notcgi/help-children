<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserController extends AbstractController
{
    public function index()
    {
        return $this->render(
            'user/index.twig',
            ['users' => $this->getDoctrine()->getRepository(User::class)->findAll()]
        );
    }

    /**
     * @param int     $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userEdit(int $id, Request $request)
    {
        $userData = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        if (!$userData) {
            throw $this->createNotFoundException(
                'Нет пользователя с id '.$id
            );
        }

        // creates a task and gives it some dummy data for this example
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add(
                'firstName',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add(
                'lastName',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add(
                'age',
                IntegerType::class,
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
                'save',
                SubmitType::class,
                [
                    'label' => 'Submit',
                    'attr' => ['class' => 'btn btn-primary'],
                ]
            )
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $userAdd = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->find($id);
            $user->setFirstName($userAdd->getFirstName());
            $user->setLastName($userAdd->getLastName());
            $user->setAge($userAdd->getAge());
            $user->setEmail($userAdd->getEmail());
            $entityManager->flush();
        }

        return $this->render(
            'panel/users/userEdit.twig',
            [
                'user' => $userData,
                'form' => $form->createView()
            ]
        );
    }
}
