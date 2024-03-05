<?php

namespace App\Form;

use App\Entity\BriefingLogo;
use App\Repository\UsuarioRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BriefingLogoType extends AbstractType
{
    private $usuarioRepository;

    public function __construct(UsuarioRepository $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre_logo', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Nombre del logo',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, ingresa una respuesta.'
                    ]),
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'El nombre del logo no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ])
            ->add('audiencia', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Ingrese la audiencia de su empresa aquí...',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, ingresa una respuesta.'
                    ]),
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'La audiencia no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ])
            ->add('ejemplo', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Ideas, referencias, ejemplos...',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, ingresa una respuesta.'
                    ]),
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'Los ejemplos no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ])
            ->add('elementos', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => '¿Qué elementos te gustaría incluir?',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, ingresa una respuesta.'
                    ]),
                    new Length([
                        'max' => 1000,
                        'maxMessage' => 'Este campo no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ])
            //->add('fecha_creacion_briefing_logo')
            /*->add('usuario', ChoiceType::class, [
                'choices' => $this->getUsuariosSinRolAdmin(),
                'choice_label' => 'username', 
            ])*/
            ->add('submit', SubmitType::class, [
                'label' => 'Enviar Briefing Logo',
                'attr' => ['class' => 'btn btn-granota btn-lg btn-block'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BriefingLogo::class,
        ]);
    }
    private function getUsuariosSinRolAdmin(): array
    {
        $usuarios = $this->usuarioRepository->findUsuariosSinRolAdmin();

        $choices = [];
        foreach ($usuarios as $usuario) {
            $choices[$usuario->getUsername()] = $usuario;
        }

        return $choices;
    }
}
