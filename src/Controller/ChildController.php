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

    public function contacts()
    {
        return $this->render('child/contacts.twig');
    }

    public function docs()
    {
        return $this->render('child/docs.twig');
    }

    public function help()
    {
        return $this->render('child/help.twig');
    }

    public function main()
    {
        return $this->render('child/main.twig');
    }

    public function kids()
    {
        return $this->render('child/kids.twig');
    }

    public function login()
    {
        return $this->render('child/login.twig');
    }

    public function news()
    {
        return $this->render('child/news.twig');
    }

    public function newscards()
    {
        return $this->render('child/newsCards.twig');
    }

    public function newsdetail()
    {
        return $this->render('child/newsdetail.twig');
    }

    public function onlinehelp()
    {
        return $this->render('child/onlineHelp.twig');
    }

    public function partners()
    {
        return $this->render('child/partners.twig');
    }

    public function reports()
    {
        return $this->render('child/reports.twig');
    }
}
