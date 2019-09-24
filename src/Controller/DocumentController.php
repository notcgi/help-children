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
use Symfony\Component\Form\FormError;
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
        $document = new Document();
        $form = $this->createForm(AddDocumentTypes::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($_FILES['add_document_types']['tmp_name'])) {
                $ff = $_FILES['add_document_types'];
                $dd = rtrim($this->getParameter('documents_directory'), '/').'/';
                $fn = $dd.uniqid().'-'.($this->translit(basename($ff['name']['file'])));
                if (move_uploaded_file($ff['tmp_name']['file'], $fn)) {
                    $document->setFile($fn);
                    $EM = $this->getDoctrine()->getManager();
                    $EM->persist($document);
                    $EM->flush();
                    return $this->redirect('/panel/documents');
                } else {
                    $ec = $_FILES['add_document_types']['error']['file'];
                    if (empty($ec)) {
                        if (!is_uploaded_file($_FILES['add_document_types']['tmp_name']['file'])) $ec = 'not uploaded';
                        if (!is_dir($dd))      $ec = 'is not dir';
                        if (!is_writable($dd)) $ec = 'not writable';
                    }
                    $dmp = array(
                        'files' => $_FILES,
                        'name'  => $fn,
                        'dir'   => $dd
                    );
                    $msg = empty($ec) ? '<pre>'.var_export($dmp, true).'</pre>' : 'Upload error: '.$ec.'!';
                    $form->get('file')->addError(new FormError($msg));
                }
            } else {
                $form->get('file')->addError(new FormError('No file to upload!'));
            }
        }

        return $this->render('panel/documents/add.twig', [
            'form'  => $form->createView()
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
                    new Assert\File(['maxSize' => '40M'])
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

    public function translit($str) {
        $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
        $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
        return str_replace($rus, $lat, $str);
    }
}
