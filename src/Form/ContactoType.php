<?php

namespace App\Form;

use App\Entity\Contacto;
use App\Repository\UsuarioRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class ContactoType extends AbstractType
{
    private $usuarioRepository;

    public function __construct(UsuarioRepository $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre_contacto', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg'],
            ])
            ->add('descripcion_contacto', TextareaType::class, [
                'attr' => ['class' => 'form-control form-control-lg'],
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control form-control-lg'],
            ])
            ->add('telefono', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg'],
            ])                    

            ->add('submit', SubmitType::class, [
                'label' => 'Crear Contacto',
                'attr' => ['class' => 'btn btn-granota btn-lg btn-block'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contacto::class,
        ]);
    }


}
