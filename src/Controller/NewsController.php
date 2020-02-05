<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\Child;
// use App\Form\NewsTypes;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;
class NewsController extends AbstractController
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     */
    // public function cards()
    // {
    //     return $this->render('news/cards.twig');
    // }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     */
    public function list()
    {
        return $this->render('news/list.twig',
            [
                'news' => $this->getDoctrine()->getRepository(News::class)->findAll()
            ]);
    }
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     */
    public function detail(int $id)
    {
        return $this->render('news/detail.twig',
            [
                'news' => $this->getDoctrine()->getRepository(News::class)->findAll(),
                'n'  => $this->getDoctrine()->getRepository(News::class)->findOneById($id)
            ]);
    }
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     */
    public function p_list()
    {
        return $this->render(
            'panel/news/list.twig',
            [
                'news' => $this->getDoctrine()->getRepository(News::class)->findAll()
            ]
        );
    }
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     */
    public function p_edit(int $id, FileUploader $fileUploader, Request $request)
    {
        $childs =[' '=>-1];
        foreach ($this->getDoctrine()->getRepository(Child::class)
            ->findAll() as $child) $childs[$child->getName()]=$child->getId();

        $n = $this->getDoctrine()
            ->getRepository(News::class)
            ->find($id);

        if (!$n) {
                $n = new News();
        }

        // $form = $this->createForm(NewsTypes::class, $n);
        $form = $this->createFormBuilder($n)
            ->add(
                'id', HiddenType::class,
                [
                    'mapped' => false
                ]
            )
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('descr', TextareaType::class, [
            ])
            // ->add('createdAt', DateType::class, [
            //     'widget' => 'single_text',
            //     'required'=>false
            // ])
            ->add(
                'child', ChoiceType::class, [
                'choices' => $childs,
                "expanded" => false,
                "multiple"=>false
             ]
            )
            ->add('photos', FileType::class, [
                'multiple' => true,
                'required'=>false,
                'constraints' => [
                    new Assert\All(
                        new Assert\File([
                            'maxSize' => '5120k',
                            'mimeTypes' => [
                                'image/png',
                                'image/jpeg',
                                'image/jpg',
                                'image/gif'
                            ],
                            'mimeTypesMessage' => 'Загружаемый файл должен быть изображением в формает PNG, JPG или GIF '
                        ])
                    )
                ]
            ])
            ->add('video', TextType::class, [
                'required'=>false])
            ->add('save', SubmitType::class, [
                'label' => 'Сохранить',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $n->getPhotos();
            echo json_encode($images);
            $arrayImg = [];
            if (!is_string($images)) foreach ($images as $image) {
                $arrayImg[] = $fileUploader->upload($image);
            }

            $n->setPhotos(json_encode($arrayImg));
            $n->setCreatedat($n->getCreatedat() ?? new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($n);
            $entityManager->flush();
            return $this->p_list();
        }

        return $this->render(
            'panel/news/edit.twig',
            [
                'n' => $n,
                'form' => $form->createView(),
                'imgs' => is_string($n->getPhotos()) ? json_decode($n->getPhotos()) : $n->getPhotos()
            ]
        );
    }
    public function delete(int $id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $n = $entityManager->getRepository(News::class)->find($id);

        if (null !== $n) {
            $entityManager->remove($n);
            $entityManager->flush();
        }
       return $this -> p_list();
    }
}
