<?php

namespace App\Form;

use App\Entity\Child;
use App\Entity\ChTarget;
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
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints as Assert;

class ChTargetTypes extends AbstractType
{
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
            ->add('child', HiddenType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('descr', TextareaType::class, [
            ])
            ->add('totime', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('collected', NumberType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Range([
                        'min' => 0,
                        'max' => 10000000
                    ])
                ]
            ])
            ->add('goal', NumberType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Range([
                        'min' => 1,
                        'max' => 10000000
                    ])
                ]
            ])
            ->add('rehabilitation', ChoiceType::class, [
                'choices' => [
                    'Реабилитация' => 1,
                    'Желание' => 0
                ],
                'constraints' => [
                    new NotBlank()
                ],
                "expanded" => true,
                "multiple"=>false
             ])
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
            ->add('save', SubmitType::class, [
                'label' => 'Submit',
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
            'data_class' => ChTarget::class
        ]);
    }
}
