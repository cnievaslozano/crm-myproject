<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
                'attr' => ['class' => 'form-control form-control-lg mb-4'],
                'label' => 'Contraseña Nueva',
                'label_attr' => ['class' => 'form-label'],
            ])

            ->add('confirm_password', PasswordType::class, [
                'attr' => ['class' => 'form-control form-control-lg', 'id' => 'confirm_password'],
                'label' => 'Confirmar Contraseña',
                'label_attr' => ['class' => 'form-label'],
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Restablecer contraseña',
                'attr' => ['class' => 'btn btn-granota btn-lg btn-block'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
