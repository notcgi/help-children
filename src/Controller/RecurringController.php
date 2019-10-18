<?php

namespace App\Controller;

use App\Entity\RecurringPayment;
use App\Entity\Request;
use App\Entity\User;
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
        set_time_limit(60);
        $rus=$this->getDoctrine()->getRepository(Request::class)->getRecRequestsWithUsers();
        $uids=[];
        foreach ($rus as $ru) {
            if(!in_array($ru['id'], $uids))  {$uids[]=$ru['id'];}
        }
        $rrs=[];
        $dat=[];
        $channels=[];
        $multi = curl_multi_init();
        foreach ($uids as $uid) {
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL,"https://api.cloudpayments.ru/subscriptions/find");
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_USERPWD, "pk_51de50fd3991dbf5b3610e65935d1:ecbe13569e824fa22e85774015784592");
          curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
          curl_setopt($ch, CURLOPT_POSTFIELDS, "accountId=".$uid);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          // $urrs = json_decode(curl_exec ($ch))->Model;
          curl_multi_add_handle($multi, $ch);
          $channels[$uid] = $ch;
      }
      $active = null;
        do {
            $mrc = curl_multi_exec($multi, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
         
        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($multi) == -1) {
                continue;
            }

            do {
                $mrc = curl_multi_exec($multi, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }
        foreach ($channels as $idx=>$urrs) {
            // echo json_encode((curl_multi_getcontent($urrs)));
            $urrs= json_decode(curl_multi_getcontent($urrs))->Model;
            if ($urrs) {
              foreach ($urrs as $urr) {
                $us=$this->getDoctrine()->getRepository(User::class)->findOneById($idx);
                $rrs[]=[
                    'uid'=> $idx,
                    'mail'=>$us->getEmail(),
                    'phone'=>$us->getPhone(),
                    'status'=>$urr->Status,
                    'sum'=>$urr->Amount,
                    'dtstart' => substr($urr->StartDateIso,0,10),
                    'dtlast' => substr($urr->LastTransactionDateIso,0,10),
                    'dtnext' => substr($urr->NextTransactionDateIso,0,10),
                    'nsuc' => $urr->SuccessfulTransactionsNumber
                ];
                $dat[]=substr($urr->LastTransactionDateIso,0,10);
              }
          }
            curl_multi_remove_handle($multi, $channels[$idx]);
        }
         
        curl_multi_close($multi);






        array_multisort($dat, SORT_DESC,$rrs); 
        return $this->render(
            'panel/recurringPayments/list.twig',
            [
                'rrs' => $rrs
            ]
            // 'panel/recurringPayments/list.twig',
            // [
            //     'recurring' => $this->getDoctrine()->getRepository(RecurringPayment::class)->findAll()
            // ]
            // 'panel/requests.twig',
            // [
            //     'entities' => $rrs
            // ]
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
