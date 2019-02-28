<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function main()
    {
        return $this->render('pages/main.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function contacts()
    {
        return $this->render('pages/contacts.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function docs()
    {
        return $this->render('pages/docs.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function help()
    {
        return $this->render('pages/help.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function partners()
    {
        return $this->render('pages/partners.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function reports()
    {
        return $this->render('pages/reports.twig');
    }
}
