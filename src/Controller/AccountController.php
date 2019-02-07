<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    public function account()
    {
        return $this->render('child/account.twig');
    }

    public function myAccount()
    {
        return $this->render('child/myAccount.twig');
    }

    public function history()
    {
        return $this->render('child/history.twig');
    }

    public function referrals()
    {
        return $this->render('child/referrals.twig');
    }

    public function recurrent()
    {
        return $this->render('child/recurrent.twig');
    }

}
