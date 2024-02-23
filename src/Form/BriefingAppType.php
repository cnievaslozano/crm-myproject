<?php

namespace App\Form;

use App\Entity\BriefingApp;
use App\Repository\UsuarioRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class BriefingAppType extends AbstractType
{
    private $usuarioRepository;

    public function __construct(UsuarioRepository $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('descripcion_empresa', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => '¿A qué se dedica la, cómo de grande es...?',
                    'style' => 'height: 150px'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, ingresa una descripción de la empresa.'
                    ]),
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'La descripción de la empresa no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ])
            ->add('descripcion_proyecto', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => '¿Cómo te gustaría que fuese el proyecto?',
                    'style' => 'height: 250px',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, ingresa una descripción del proyecto.'
                    ]),
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'La descripción de la empresa no puede tener más de {{ limit }} caracteres.'
                    ])
                ]

            ])
            ->add('objetivos', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => '¿Cuál es el objetivo principal de tu empresa?',
                    'style' => 'height: 150px',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, ingresa unos objetivos de la empresa.'
                    ]),
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'Los objetivos de la empresa no puede tener más de {{ limit }} caracteres.'
                    ])
                ]

            ])
            ->add('competencia', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => '¿Quién es tu competencia? Breve análisis de las mejoras webs del sector.',
                    'style' => 'height: 150px',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, ingresa una respuesta.'
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'La descripción de la competencia no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ])
            ->add('naming_slogan', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Eslogan',
                ],
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'El naming / slogan no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ])
            ->add('imagen_logotipo_ruta', FileType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg'
                ], 
                'required' => false,
            ])
            ->add('gama_cromatica', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => '¿Qué colores utilizará la aplicación?',
                    'style' => 'height: 150px',
                ],
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 200,
                        'maxMessage' => 'La gama cromática no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ])
            ->add('estructura_app', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Explica detalladamente como te gustaría que fuese la app.',
                    'style' => 'height: 250px',
                ],
                'required' => false,
            ])
            //->add('fecha_creacion_briefing_app')
            /*->add('usuario', ChoiceType::class, [
                'choices' => $this->getUsuariosSinRolAdmin(),
                'choice_label' => 'username', 
            ])*/
            ->add('submit', SubmitType::class, [
                'label' => 'Enviar Briefing App',
                'attr' => ['class' => 'btn custom-btn btn-lg btn-block'],
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BriefingApp::class,
        ]);
    }
}
