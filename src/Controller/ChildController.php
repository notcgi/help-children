<?php

namespace App\Controller;

use App\Entity\Child;
use App\Entity\User;
use App\Form\AddChildTypes;
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

        return $this->render(
            'child/detail.twig',
            [
                'child' => $child,
                'form' => [
                    'payment-type' => 'visa'
                ]
            ]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
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
    public function edit(int $id, Request $request)
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
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($childData);
            $entityManager->flush();
        }

        return $this->render(
            'panel/child/edit.twig',
            [
                'child' => $childData,
                'form' => $form->createView()
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
                // $users = $this->getDoctrine()->getRepository(User::class)->getAll();
                // foreach ($users as $user) {
                //     $mail = $sg->getMail(
                //         $user->getEmail(),
                //         $user->getFirstName(),
                //         [
                //             'first_name' => $user    ->getFirstName(),
                //             'name'       => $userData->getName(),
                //             'age'        => $userData->getAge(),
                //             'diag'       => $userData->getDiagnosis(),
                //             'place'      => $userData->getCity(),
                //             'goal'       => (int) $userData->getGoal(),
                //             'photo'      => $userData->getImages()[0],
                //             'id'         => $userData->getId()
                //         ]
                //     );
                //     $mail->setTemplateId('d-8b30e88d3754462790edc69f7fe55540');
                //     $sg->send($mail);
                // }
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
}
