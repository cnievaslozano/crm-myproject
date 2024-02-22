<?php

namespace App\Form;

use App\Entity\Empresa;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmpresaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Nombre',
                ],
            ])
            ->add('imagen_logotipo_ruta', FileType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'required' => false,
                ],
            ])
            ->add('descripcion_empresa', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Descripción de la empresa',
                    'style' => 'height: 250px',
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Email',
                ],
            ])
            ->add('telefono', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Teléfono',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Crear Empresa',
                'attr' => ['class' => 'btn custom-btn btn-lg btn-block'],
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Empresa::class,
        ]);
    }
}
