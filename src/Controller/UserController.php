<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\User;

class UserController extends AbstractController
{
    public function index()
    {
        return $this->render(
            'user/index.twig',
            ['users' => $this->getDoctrine()->getRepository(User::class)->findAll()]
        );
    }
}
