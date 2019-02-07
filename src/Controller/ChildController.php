<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ChildController extends AbstractController
{
    public function view()
    {
        return $this->render('child/view.twig');
    }

    public function registration()
    {
        return $this->render('child/registration.twig');
    }
}
