<?php

namespace App\Controller;

use App\Entity\Document;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Repository\DocumentRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class DocumentController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function index()
    {
        return $this->render(
            'panel/documents/index.twig',
            [
                'documents' => $this->getDoctrine()->getRepository(Document::class)->findAll()
            ]
        );
    }

    /**
     * @param int     $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     */
    public function edit(int $id, Request $request)
    {
        /** @var DocumentRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Document::class);
        $document   = $repository->find($id);

        if (!$document) throw $this->createNotFoundException('Нет документа с id '.$id);

        $form = $this->createFormBuilder($document)
            ->add('id', HiddenType::class, ['mapped' => false, 'constraints' => [new NotBlank()]])
            ->add('title', TextType::class, ['constraints' => [new NotBlank()]])
            ->add('description', TextareaType::class, ['constraints' => []])
            ->add('category', ChoiceType::class, ['choices' => [
                'Финансовые отчёты'      => 'financial',
                'Аудиторские заключения' => 'auditor'
            ]])
            ->add('file', FileType::class, [
                'multiple' => false,
                'constraints' => [
                    new NotBlank(),
                    new Assert\All(
                        new Assert\File([
                            'maxSize' => '5120k',
/*                            'mimeTypes' => [
                                'image/png',
                                'image/jpeg',
                                'image/jpg',
                                'image/gif'
                            ],
                            'mimeTypesMessage' => 'Загружаемый файл должен быть изображением в формает PNG, JPG или GIF ' */
                        ])
                    )
                ]

            ])
            ->add('save', SubmitType::class, [
                'label' => 'Сохранить',
                'attr' => ['class' => 'btn btn-primary']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($document);
            $entityManager->flush();
        }

        return $this->render(
            'panel/documents/edit.twig',
            [
                'document' => $document,
                'form'     => $form->createView()
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
        /** @var DocumentRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Document::class);
        $document   = $repository->find($id);

        if (!$document) throw $this->createNotFoundException('Нет документа с id '.$id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($document);
        $entityManager->flush();

        return $this->redirect('/panel/documents');
    }
}
