<?php

namespace App\Controller;

use App\Entity\Child;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChildController extends AbstractController
{
    public function view($id)
    {
        $child = $this->getDoctrine()
            ->getRepository(Child::class)
            ->find($id);

        if (!$child) {
            throw $this->createNotFoundException(
                'Нет ребенка с id ' . $id
            );
        }

        $leftGoal = $child->getGoal() - $child->getCollected();
        $statusGoal = $this->statusGoal($child->getCollected(), $child->getGoal());
        $ageChildren = $this->ageChildren($child->getBirthdate(), date_create(date('Y-m-d')));

        return $this->render(
            'child/view.twig',
            [
                'child' => $child,
                'ageChildren' => $ageChildren,
                'leftGoal' => $leftGoal > 0 ? $leftGoal : 0,
                'statusGoal' => $statusGoal
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

    public function statusGoal($collected = 0, $goal = 0)
    {
        if ($collected < 0) {
            return 0;
        }

        if ($goal <= 0) {
            return 0;
        }
        if ($collected > $goal) {
            return 100;
        }

        return 100 * $collected / $goal;
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
