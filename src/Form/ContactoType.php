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
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

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
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, ingresa una respuesta.'
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Este campo no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ])
            ->add('descripcion_contacto', TextareaType::class, [
                'attr' => ['class' => 'form-control form-control-lg'],
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Este campo no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control form-control-lg'],
                'required' => false, 
                'constraints' => [
                    new Length([
                        'max' => 150,
                        'maxMessage' => 'Este campo no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ])
            ->add('telefono', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg'],
                'required' => false, 
                'constraints' => [
                    new Length([
                        'max' => 9,
                        'maxMessage' => 'Este campo no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Crear Contacto',
                'attr' => ['class' => 'btn btn-granota btn-lg btn-block'],
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contacto::class,
        ]);
    }
}
