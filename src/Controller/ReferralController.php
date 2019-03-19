<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ReferralController extends AbstractController
{
    /**
     * @param int              $id
     * @param SessionInterface $session
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function devourer(int $id, SessionInterface $session, UrlGeneratorInterface $generator)
    {
        if (0 < $id) {
            $session->set('referral', $id);
        }

        return $this->redirect($generator->generate('donate'));
    }
}
