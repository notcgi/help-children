<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ReferralController extends AbstractController
{

    /**
     * @param int              $id
     * @param SessionInterface $session
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function devourer(int $id, SessionInterface $session)
    {
        if($id > 0){
            $session->set('referral', $id);
        }
        return $this->render('pages/main.twig');
    }
}
