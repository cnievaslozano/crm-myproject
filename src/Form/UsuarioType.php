<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg'],
            ])
            //->add('email', EmailType::class) // relacion contactos no lo pilla
            //->add('roles')
            ->add('nombre_usuario', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg'],
            ])
            ->add('apellidos_usuario', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg'],
            ])
            //->add('activo')
            //->add('fecha_creacion_usuario')
            ->add('password', PasswordType::class, [
                'attr' => ['class' => 'form-control form-control-lg'],
            ])
            // ->add('empresa') relacion no lo pilla
            //->add('briefing_web')
            //->add('briefing_app')
            //->add('briefing_logo')
            ->add('submit', SubmitType::class, [
                'label' => 'Crear Usuario',
                'attr' => ['class' => 'btn custom-btn btn-lg btn-block'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
