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
    public function detail(int $id)
    {
        $child = $this->getDoctrine()
            ->getRepository(Child::class)
            ->find($id);

        if (!$child) {
            throw $this->createNotFoundException(
                'Нет ребенка с id '.$id
            );
        }

        return $this->render(
            'child/detail.twig',
            [
                'child' => $child,

            ]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     */
    public function list()
    {
        return $this->render(
            'child/list.twig',
            [
                'children' => $this->getDoctrine()->getRepository(Child::class)->findAll(),
            ]
        );
    }

    public function login()
    {
        return $this->render('child/login.twig');
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
