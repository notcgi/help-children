<?php

namespace App\Controller;

use App\Entity\Child;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


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
        return $this->render(
            'child/list.twig',
            [
                'children' => $this->getDoctrine()->getRepository(Child::class)->findAll()
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

        $form = $this->createFormBuilder($childData)
            ->add(
                'id',
                HiddenType::class,
                [
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add('birthdate',
                DateType::class,
                ['widget' => 'single_text']
            )
            ->add(
                'diagnosis',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            /*->add(
                'images',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )*/
            ->add(
                'comment',
                TextareaType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add(
                'requisites',
                TextareaType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add(
                'contacts',
                TextareaType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Submit',
                    'attr' => ['class' => 'btn btn-primary'],
                ]
            )
            ->getForm();

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
                'form' => $form->createView(),
            ]
        );
    }
}
