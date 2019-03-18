<?php

namespace App\Form;

use App\Entity\Child;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints as Assert;

class AddChildTypes extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('birthdate', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('diagnosis', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('images', FileType::class, [
                'multiple' => true,
                'constraints' => [
                    new NotBlank(),
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
            ->add('comment', TextareaType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('requisites', TextareaType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('contacts', TextareaType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('collected', NumberType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Range([
                        'min' => 1,
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
            ->add('save', SubmitType::class, [
                'label' => 'Submit',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Child::class
        ]);
    }
}
