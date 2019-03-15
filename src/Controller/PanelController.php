<?php

namespace App\Controller;

use App\Entity\RecurringPayment;
use App\Entity\ReferralHistory;
use App\Entity\Request;
use App\Entity\User;
use App\Repository\RecurringPaymentsRepository;
use App\Repository\RequestRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanelController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \LogicException
     */
    public function main()
    {
        $doctrine = $this->getDoctrine();
        /** @var RequestRepository $requestRepository */
        $requestRepository = $doctrine->getRepository(Request::class);
        /** @var RecurringPaymentsRepository $recurringRepository */
        $recurringRepository = $doctrine->getRepository(RecurringPayment::class);
        /** @var UserRepository $userRepository */
        $userRepository = $doctrine->getRepository(User::class);

        return $this->render(
            'panel/main.twig',
            [
                'totalSum' => $requestRepository->aggregateSumSuccessPayment(),
                'referralSum' => $doctrine->getRepository(ReferralHistory::class)->aggregateSum(),
                'recurringSum' => $recurringRepository->aggregateSum(),
                'totalAvg' => $requestRepository->aggregateAvgSuccessPayment(),
                'recurringSumCount' => $recurringRepository->count([]),
                'userBaseCount' => $userRepository->count(['referrer' => null]),
                'userRefCount' => $userRepository->countReferrerNotNull()
            ]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function users()
    {
        return $this->render(
            'panel/users/users.twig',
            ['users' => $this->getDoctrine()->getRepository(User::class)->findAll()]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function requests()
    {
        /** @var RequestRepository $repository */
        $repository = $this->getDoctrine()->getRepository(\App\Entity\Request::class);

        return $this->render(
            'panel/requests.twig',
            [
                'entities' => $repository->getRequestsWithUsers(),
            ]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function payments()
    {
        return $this->render(
            'panel/payments.twig'
        );
    }
}
