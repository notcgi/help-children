<?php

namespace App\Controller;

use App\Entity\Child;
use App\Form\AddChildTypes;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Range;
use App\Service\FileUploader;

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
     * @throws \LogicException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
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
                        new NotBlank()
                    ]
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank()
                    ]
                ]
            )
            ->add(
                'birthdate',
                DateType::class,
                [
                    'widget' => 'single_text'
                ]
            )
            ->add(
                'diagnosis',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank()
                    ]
                ]
            )
            /*->add(
                'images',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank()
                    ]
                ]
            )*/
            ->add(
                'comment',
                TextareaType::class,
                [
                    'constraints' => [
                        new NotBlank()
                    ]
                ]
            )
            ->add(
                'requisites',
                TextareaType::class,
                [
                    'constraints' => [
                        new NotBlank()
                    ]
                ]
            )
            ->add(
                'contacts',
                TextareaType::class,
                [
                    'constraints' => [
                        new NotBlank()
                    ]
                ]
            )
            ->add(
                'collected',
                NumberType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Range([
                            'min' => 1,
                            'max' => 10000000
                        ])
                    ]
                ]
            )
            ->add(
                'goal',
                NumberType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Range([
                            'min' => 1,
                            'max' => 10000000
                        ])
                    ]
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Submit',
                    'attr' => [
                        'class' => 'btn btn-primary'
                    ]
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
                'form' => $form->createView()
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
    public function add(Request $request, FileUploader $fileUploader)
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
