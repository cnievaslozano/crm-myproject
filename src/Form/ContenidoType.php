<?php

namespace App\Form;

use App\Entity\Contenido;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ContenidoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, rellena este campo.'
                    ]),
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'Este campo no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ])
            ->add('punto_menu', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, rellena este campo.'
                    ]),
                    new Length([
                        'max' => 20,
                        'maxMessage' => 'Este campo no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ])
            ->add('contenido', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'style' => 'height: 500px',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, rellena este campo.'
                    ]),
                ]
            ])
            ->add('ruta_imagenes_contenidos', FileType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'required' => false,
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enviar Contenido',
                'attr' => ['class' => 'btn custom-btn btn-lg btn-block'],
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contenido::class,
        ]);
    }
}
