<?php

namespace App\Form;

use App\Entity\Incidencia;
use App\Repository\UsuarioRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class IncidenciaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('tipo')
            ->add('titulo', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                ],
            ])
            ->add('descripcion', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => '¿Cuál es el problema?',
                    'style' => 'height: 250px'
                ],
            ])
            ->add('url', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Enlace/s a la web/s que da/n problema/s',
                ],
            ])

            ->add('tipo', ChoiceType::class, [
                'choices' => [
                    'Web' => 'Web',
                    'App' => 'App',
                ],
                'attr' => [
                    'class' => 'form-control form-control-lg',
                ],
            ])

            ->add('ruta_imagenes', FileType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control form-control-lg'
                ],
            ])
            //->add('fecha_creacion_incidencia')
            //->add('briefing_web')
            //->add('briefing_app')
            ->add('submit', SubmitType::class, [
                'label' => 'Enviar Incidencia',
                'attr' => ['class' => 'btn custom-btn btn-lg btn-block'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Incidencia::class,
        ]);
    }
}
