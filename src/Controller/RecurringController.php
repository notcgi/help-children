<?php

namespace App\Controller;

use App\Entity\RecurringPayment;
use App\Entity\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecurringController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     */
    public function list()
    {
        
        $repository = $this->getDoctrine()->getRepository(Request::class);
        return $this->render(
            // 'panel/recurringPayments/list.twig',
            // [
            //     'recurring' => $this->getDoctrine()->getRepository(RecurringPayment::class)->findAll()
            // ]
            'panel/requests.twig',
            [
                'entities' => $repository->getRecRequestsWithUsers()
            ]
        );
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \LogicException
     */
    public function delete(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $product = $entityManager->getRepository(RecurringPayment::class)->find($id);

        if (null !== $product) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirect('/panel/recurring');
    }
}
