<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\User;
use App\Form\AddDocumentTypes;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\SendGridSchedule;
use App\Service\SendGridService;
use App\Repository\DocumentRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

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

    public function add(Request $request, FileUploader $uploader, SendGridService $sg) {
        $document = new Document();
        $form = $this->createForm(AddDocumentTypes::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $doc */
            $doc = $form->get('file')->getData();
            $fn  = $this->translit(pathinfo($doc->getClientOriginalName(), PATHINFO_FILENAME));
            $fn  = $fn.'-'.uniqid().'.'.$doc->guessExtension();
            try {
                $doc->move($this->getParameter('documents_directory'), $fn);
                $document->setFile('/docs/'.$fn);
                $EM = $this->getDoctrine()->getManager();
                $EM->persist($document);
                $EM->flush();

                // SEND MAIL 12
                $users = $this->getDoctrine()->getRepository(User::class)->getAll();
                foreach ($users as $user) {
                    $mail = $sg->getMail(
                        $user->getEmail(),
                        $user->getFirstName(),
                        [
                            'first_name' => $user->getFirstName(),
                            'docdate'    => $document->getNameDate(),
                            'docname'    => $document->getTitle(),
                            'docsrc'     => $document->getFile(),
                            'fs'         => $document->getFilesize()
                        ]
                    );
                    $mail->setTemplateId('d-af64459f4a5c46158550ce4336c17892');
                    $sg->send($mail);
                }
                // END SEND
                
                return $this->redirect('/panel/documents');
            } catch (FileException $e) {
                $form->get('file')->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('panel/documents/add.twig', [
            'form'  => $form->createView(),
            'document'=>$document
        ]);
    }

    protected function old_upload(FormInterface $form, Document $document, $ffn) {
        if (!empty($_FILES[$ffn]['tmp_name']['file'])) {
            $dd = rtrim($this->getParameter('documents_directory'), '/').'/';
            $fn = $this->translit(basename($_FILES[$ffn]['name']['file']));
            $fn = $dd.uniqid().'-'.preg_replace('~\s+~si', '_', $fn);
            if (!is_uploaded_file($_FILES[$ffn]['tmp_name']['file'])) $ec = 'not uploaded';
            if (!is_dir($dd))                                         $ec = 'is not dir';
            if (!is_writable($dd))                                    $ec = 'not writable';
            $fs = filesize($_FILES[$ffn]['tmp_name']['file']);
            if (empty($ec)) {
                if (\move_uploaded_file($_FILES[$ffn]['tmp_name']['file'], $fn)) {
                    $document->setFile($fn);
                    $EM = $this->getDoctrine()->getManager();
                    $EM->persist($document);
                    $EM->flush();
                    return $this->redirect('/panel/documents');
                } else {
                    $ec = $_FILES[$ffn]['error']['file'];
                    $dmp = array(
                        'files' => $_FILES,
                        'name'  => $fn,
                        'dir'   => $dd,
                        'size'  => $fs
                    );
                    $msg = empty($ec) ? var_export($dmp, true) : 'Upload error: '.$ec.'!';
                    $form->get('file')->addError(new FormError($msg));
                }
            } else { $form->get('file')->addError(new FormError('Error: '.$ec)); }
        } else { $form->get('file')->addError(new FormError('No file to upload!')); }
    }

    protected function correctName($fn, $dir = '') {
        $fn = $this->translit(basename($fn));
        if (!empty($dir)) $dir = (rtrim($dir, '/').'/');
        return $dir.uniqid().'-'.preg_replace('~\s+~si', '_', $fn);
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
            ->add('date'        , DateType::class    , [
                'html5' => True,
                'widget' => 'choice',
                'placeholder' => [
                    'year' => 'Год', 'month' => 'Месяц', 'day' => 'День',
                ],
                'format' => 'dd . MM . yyyy'])
            ->add('category'    , ChoiceType::class   , ['choices' => Document::TYPES])
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
            return $this->redirect('/panel/documents');
        }

        return $this->render(
            'panel/documents/edit.twig',
            [
                'form'     => $form->createView(),
                'document' => $document
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
        return preg_replace('#\s+#si', '_', str_replace($rus, $lat, $str));
    }
}
