<?php

namespace App\Controller;

use App\Entity\Config;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ConfigController extends AbstractController
{

    public function edit(Request $request)
    {
        $config = $this->getDoctrine()
            ->getRepository(Config::class)
            ->findOneBy(['id' => 1]);

        if (!$config) {
            throw $this->createNotFoundException(
                'Ошибка загрузки конфигурации'
            );
        }

        $form = $this->createFormBuilder($config)
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
                'percentDefault',
                NumberType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Range([
                            'min' => 0,
                            'max' => 100
                        ])
                    ]
                ]
            )
            ->add(
                'percentRecurrent',
                NumberType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Range([
                            'min' => 0,
                            'max' => 100
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
            $entityManager->persist($config);
            $entityManager->flush();
        }

        return $this->render(
            'panel/config.twig',
            [
                'config' => $config,
                'form' => $form->createView()
            ]
        );
    }
}
