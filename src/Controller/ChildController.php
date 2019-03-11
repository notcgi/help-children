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
                'child' => $child
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
        $opened = $closed = [];

        /** @var Child $child */
        foreach ($this->getDoctrine()->getRepository(Child::class)->findAll() as $child) {
            $child->isOpened() ? $opened[] = $child : $closed[] = $child;
        }

        return $this->render(
            'child/list.twig',
            [
                'opened' => $opened,
                'closed' => $closed
            ]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     */
    public function list_panel()
    {
        return $this->render(
            'panel/child/list.twig',
            [
                'children' => $this->getDoctrine()->getRepository(Child::class)->findAll()
            ]
        );
    }
}
