<?php

namespace App\Form;

use App\Entity\News;
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

class NewsTypes extends AbstractType
{
//     public function __construct(Array $childs){
//     $this->childs = $childs; 
// }
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       
        $builder
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
            ->add('createdat',HiddenType::class,)
            ->add(
                'child', ChoiceType::class, [
                'choices' => $this->childs,
                "expanded" => false,
                "multiple"=>false
             ]
            )
            ->add('attach', FileType::class, [
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
            ->add('video', TextType::class)
            ->add('save', SubmitType::class, [
                'label' => 'Сохранить',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => News::class
        ]);
    }
}
