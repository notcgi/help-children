<?php

namespace App\Form;

use App\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;

class AddDocumentTypes extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title'       , TextType::class     , ['constraints' => [new NotBlank()]])
            ->add('date'        , DateType::class    , [
                'html5' => True,
                'widget' => 'choice',
                'placeholder' => [
                    'year' => 'Год', 'month' => 'Месяц', 'day' => 'День',
                ],
                'format' => 'dd . MM . yyyy'])
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
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Document::class
        ]);
    }
}
