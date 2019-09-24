<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\AddDocumentTypes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Repository\DocumentRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

    public function add(Request $request) {
        $documentData = new Document();
        $form = $this->createForm(AddDocumentTypes::class, $documentData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (false) {
                /*
'add_document_types' =>
  array (
    'name' =>
    array (
      'file' => 'Наш Сосед.pdf',
    ),
    'type' =>
    array (
      'file' => 'application/pdf',
    ),
    'tmp_name' =>
    array (
      'file' => '/tmp/phpgn81qc',
    ),
    'error' =>
    array (
      'file' => 0,
    ),
    'size' =>
    array (
      'file' => 329848,
    ),
  ),
                                 */
                /** @var UploadedFile $image */
                if ($image = $form['file']->getData()) {
                    $fName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    $fName = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $fName);
                    $fName.= '-'.uniqid().'.'.$image->guessExtension();
                    try {
                        $image->move($this->getParameter('documents_directory'), $fName);
                        $documentData->setFile($fName);
                    } catch (FileException $e) {
                        // Here is reclaim :-+
                    }
                }
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($documentData);
                $entityManager->flush();
                return $this->redirect('/panel/documents');
            }
        }

        return $this->render('panel/documents/add.twig', [
            'form'  => $form->createView(),
            'files' => empty($_FILES) ? 'no_files' : var_export($_FILES, true)
        ]);
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
            ->add('id'          , HiddenType::class   , ['mapped' => false, 'constraints' => [new NotBlank()]])
            ->add('title'       , TextType::class     , ['constraints' => [new NotBlank()]])
            ->add('description' , TextareaType::class)
            ->add('category'    , ChoiceType::class   , ['choices' => Document::TYPES])
            ->add('file'        , FileType::class     , [
                'multiple'    => false,
                'constraints' => [
                    new NotBlank(),
                    new Assert\All(new Assert\File(['maxSize' => '40000k']))
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Сохранить',
                'attr'  => ['class' => 'btn btn-primary']
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
