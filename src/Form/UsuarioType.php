<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            //->add('email', EmailType::class) // relacion contactos no lo pilla
            //->add('roles')
            ->add('nombre_usuario')
            ->add('apellidos_usuario')
            //->add('activo')
            //->add('fecha_creacion_usuario')
            ->add('password', PasswordType::class)
            // ->add('empresa') relacion no lo pilla
            ->add('Crear-Usuario', SubmitType::class)
            //->add('briefing_web')
            //->add('briefing_app')
            //->add('briefing_logo')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
