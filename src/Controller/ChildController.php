<?php

namespace App\Controller;

use App\Entity\Child;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChildController extends AbstractController
{
    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function view(int $id)
    {
        $child = $this->getDoctrine()
            ->getRepository(Child::class)
            ->find($id);

        if (!$child) {
            throw $this->createNotFoundException(
                'Нет ребенка с id ' . $id
            );
        }

        return $this->render(
            'child/view.twig',
            [
                'child' => $child
            ]
        );
    }

    public function ageChildren($birthDate, $todayDate)
    {
        if ($birthDate >= $todayDate) {
            return 0;
        }

        $ageChildren = date_diff($birthDate, $todayDate);

        return $ageChildren->y;
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
        return $this->render(
            'child/kids.twig',
            ['kids' => $this->getDoctrine()->getRepository(Child::class)->findAll()]
        );
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
