<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Task;
//use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PanelController extends AbstractController
{
    public function auth()
    {
        // creates a task and gives it some dummy data for this example
        $task = new Task();

        $form = $this->createFormBuilder($task)
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('save', SubmitType::class, ['label' => 'Submit', 'attr'=>['class'=>'btn btn-primary']])
            ->getForm();

        return $this->render('panel/auth.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function users()
    {
        return $this->render('panel/users.twig');
    }
}
