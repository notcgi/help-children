<?php

namespace App\Controller;

use App\Entity\Child;
use App\Entity\User;
use App\Entity\News;
use App\Entity\ChTarget;
use App\Form\AddChildTypes;
use App\Form\ChTargetTypes;
use App\Form\EditChildTypes;
use App\Service\SendGridService;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ChildController extends AbstractController
{
    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
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
        $trg=$this->getDoctrine()->getRepository(ChTarget::class)->findByChild($child);
        $ctarg=end($trg);
        if (($ctarg->getCollected()>=$ctarg->getGoal()) && ($ctarg->getAllowClose()[0])) $state='close';
        else $state= ($ctarg->getRehabilitation()) ? 'rehab' : 'pmj';
        $child_lst=$this->getDoctrine()->getRepository(Child::class)->getCurCh($state);
        $key=0;
        foreach ($child_lst as $key => $ch) {
            if ($ch['id'] == $id)  break;
        }
        return $this->render(
            'child/detail.twig',
            [
                'child' => $child,
                'form' => [
                    'payment-type' => 'visa'
                ],
                'yo'=>$child->getAge() . ' ' . ['год', 'года', 'лет'][ (($child->getAge())%100>4 && ($child->getAge())%100<20)? 2: [2, 0, 1, 1, 1, 2][min($child->getAge()%10, 5)]],
                'targets' => $trg,
                'imgs' => json_decode(end($trg)->getAttach()),
                'prevnext'=>[($key==0) ? $child_lst[(count($child_lst)-1)]['id'] :  $child_lst[($key-1) % (count($child_lst)-1)]['id'],$child_lst[($key+1) % (count($child_lst))]['id']],
                'closed'=> $state=='close',
                'title'=>['close'=>"Мы помогли",'pmj'=>"Подарки, желания, мечты",'rehab'=>"Долгосрочная опека"][$state],
                "news_lst" => $this->getDoctrine()->getRepository(News::class)->findByChild($child)
            ]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function list()
    {
        return $this->render(
            'child/list.twig',
            [
                'opened' => $this->getDoctrine()->getRepository(Child::class)->getCurCh('rehab'),
                'pmj' => $this->getDoctrine()->getRepository(Child::class)->getCurCh('pmj'),
                'closed' => $this->getDoctrine()->getRepository(Child::class)->getCurCh('close')
            ]
        );  
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
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

    /**
     * @param int     $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(int $id, FileUploader $fileUploader, Request $request)
    {
        $childData = $this->getDoctrine()
            ->getRepository(Child::class)
            ->find($id);

        if (!$childData) {
            throw $this->createNotFoundException(
                'Нет ребенка с id '.$id
            );
        }

        $form = $this->createForm(EditChildTypes::class, $childData);
        $oldimages = $childData->getImages();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $childData->getImages();
            $arrayImg = [];
            if (!is_string($images)) foreach ($images as $image) {
                $arrayImg[] = $fileUploader->upload($image);
            }
            if (!is_string($oldimages)) foreach ($oldimages as $image) {
                $arrayImg[] = $image;
            }
            $childData->setImages($arrayImg); 
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($childData);
            $entityManager->flush();
        }

        return $this->render(
            'panel/child/edit.twig',
            [
                'child' => $childData,
                'form' => $form->createView(),
                'targets' => $this->getDoctrine()->getRepository(ChTarget::class)->findByChild($childData)
            ]
        );
    }
    public function target(int $id, int $child, FileUploader $fileUploader, Request $request)
    {
        $childData = $this->getDoctrine()
            ->getRepository(Child::class)
            ->find($child);

        if (!$childData) {
            throw $this->createNotFoundException(
                'Нет ребенка с id '.$child
            );
        }
        $targ = $this->getDoctrine()
            ->getRepository(ChTarget::class)
            ->find($id);

        if (!$targ) {
                $targ = new ChTarget();
        }

        $form = $this->createForm(ChTargetTypes::class, $targ);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $targ->getAttach();
            $arrayImg = [];
            // echo $images;
            if (!is_string($images)) foreach ($images as $image) {
                $arrayImg[] = $fileUploader->upload($image);
            }

            $targ->setAttach(json_encode($arrayImg));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($targ);
            $entityManager->flush();
            return $this->edit($child, $fileUploader, $request);
        }

        return $this->render(
            'panel/child/target.twig',
            [
                'target' => $targ,
                'child' => $childData,
                'form' => $form->createView(),
                'imgs' => is_string($targ->getAttach()) ? json_decode($targ->getAttach()) : $targ->getAttach()
            ]
        );
    }

    public function delete(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $child = $entityManager->getRepository(Child::class)->find($id);

        if (null !== $child) {
            $entityManager->remove($child);
            $entityManager->flush();
        }
        return $this->render(
            'panel/child/list.twig',
            [
                'children' => $this->getDoctrine()->getRepository(Child::class)->findAll()
            ]
        );
    }
    public function deltrg(int $child, int $id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $trg = $entityManager->getRepository(ChTarget::class)->find($id);

        if (null !== $trg) {
            $entityManager->remove($trg);
            $entityManager->flush();
        }
       return $this -> edit( $child, $request);
    }


    /**
     * @param Request      $request
     * @param FileUploader $fileUploader
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function add(Request $request, FileUploader $fileUploader, SendGridService $sg)
    {
        $userData = new Child();
        $form = $this->createForm(AddChildTypes::class, $userData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $userData->getImages();
            $arrayImg = [];

            foreach ($images as $image) {
                $arrayImg[] = $fileUploader->upload($image);
            }

            $userData->setImages($arrayImg);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($userData);
            $entityManager->flush();

                // SEND MAIL 12
                $users = $this->getDoctrine()->getRepository(User::class)->getAll();
                foreach ($users as $user) {
                    $mail = $sg->getMail(
                        $user->getEmail(),
                        $user->getFirstName(),
                        [
                            'first_name' => $user    ->getFirstName(),
                            'name'       => $userData->getName(),
                            'age'        => $userData->getAge(),
                            'diag'       => $userData->getDiagnosis(),
                            'place'      => $userData->getCity(),
                            'goal'       => (int) $userData->getGoal(),
                            'photo'      => $userData->getImages()[0],
                            'id'         => $userData->getId(),
                            'url'        => $user->getDonateUrl()
                        ]
                    );
                    $mail->setTemplateId('d-8b30e88d3754462790edc69f7fe55540');
                    $sg->send($mail);
                }
                // END SEND
                
            return $this->redirect('/panel/child');
        }

        return $this->render(
            'panel/child/add.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
    public function delimg(int $id, $img, Request $request)
    {
        $n = $this->getDoctrine()->getRepository(Child::class)->find($id);
        $nar=$n->getImages();
        unset($nar[$img]);
        $n->setImages($nar);
        $this->getDoctrine()->getManager()->flush();
       return $this -> list_panel();
    }
}
