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
        // $data=[];
        $users=$this->getDoctrine()->getRepository(User::class)->findBy([], ['createdAt' => 'DESC']);;
        // foreach ($users as $idx => $us) {

        //   $ch = curl_init();
        //   curl_setopt($ch, CURLOPT_URL,"https://api.cloudpayments.ru/subscriptions/find");
        //   curl_setopt($ch, CURLOPT_POST, 1);
        //   curl_setopt($ch, CURLOPT_USERPWD, "pk_51de50fd3991dbf5b3610e65935d1:ecbe13569e824fa22e85774015784592");
        //   curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        //   curl_setopt($ch, CURLOPT_POSTFIELDS, "accountId=".$this->getUser()->getId());
        //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //   $urrs = json_decode(curl_exec ($ch))->Model;

        //   $rrs=[];
        //   curl_close ($ch);
        //   if ($urrs) {
        //       foreach ($urrs as $urr) {
        //         if ($urr->Status=="Active")
        //         $rrs[]=[
        //             'id'=> $urr->Id,
        //             'status'=>$urr->Status,
        //             'sum'=>$urr->Amount,
        //         ];
        //       }
        //   }
        //   $hasrec= ($rrs!=[]);
        //     $data[$idx]=[
        //         "firstName"=>$us->getFirstName(),
        //         "lastName"=>$us->getlastName(),
        //         "email"=>$us->getemail(),
        //         "birthday"=>$us->getbirthday(),
        //         "Phone"=>$us->getPhone(),
        //         "CreatedAt"=>$us->getCreatedAt(),
        //         "id"=>$us->getid(),
        //         "hasrec"=>$hasrec
        //     ];
        // }
        return $this->render(
            'panel/users/users.twig',
            [
                'users' => $users
                // 'users' => $data
            ]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function requests()
    {
        /** @var RequestRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Request::class);

        return $this->render(
            'panel/requests.twig',
            [
                'entities' => $repository->getRequestsWithUsers()
            ]
        );
    }
}
