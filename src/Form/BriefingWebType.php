<?php

namespace App\Form;

use App\Entity\BriefingWeb;
use App\Repository\UsuarioRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class BriefingWebType extends AbstractType
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
                    'placeholder' => '¿A qué se dedica, cómo de grande es...?',
                    'style' => 'height: 150px'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, ingresa una respuesta.'
                    ]),
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'Este campo no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ])
            ->add('descripcion_proyecto', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => '¿Cómo te gustaría que fuese el proyecto?',
                    'style' => 'height: 250px',
                ],
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'Este campo no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ])
            ->add('productos_y_o_servicios', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => '¿Qué productos y/o servicios ofrece?',
                    'style' => 'height: 250px',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, rellena este campo.'
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Este campo no puede tener más de {{ limit }} caracteres.'
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
                        'message' => 'Por favor, rellena este campo.'
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Este campo no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ])
            ->add('objetivos', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'style' => 'height: 150px',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, rellena este campo.'
                    ]),
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'Este campo no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ]) 
            ->add('web_ejemplo', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'style' => 'height: 250px',
                ],
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'Este campo no puede tener más de {{ limit }} caracteres.'
                    ]),
                ] 
                  
            ])
            ->add('estructura_y_contenido', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'style' => 'height: 300px',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, rellena este campo.'
                    ]),
                ]
            ])
            ->add('funciones', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'style' => 'height: 250px',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, rellena este campo.'
                    ]),
                ]
            ])
            // --------------
            ->add('disponeLogo', ChoiceType::class, [
                'label' => '¿Dispone de logo?',
                'choices' => [
                    'Sí' => true,
                    'No' => false,
                ],
                'expanded' => false,
                'multiple' => false, 
                'data' => false,
            ])
            ->add('disponeManualMarca', ChoiceType::class, [
                'label' => '¿Dispone de manual de marca corporativa?',
                'choices' => [
                    'Sí' => true,
                    'No' => false,
                ],
                'expanded' => false,
                'multiple' => false,
                'data' => false,
            ])
            ->add('disponeColoresCorporativos', ChoiceType::class, [
                'label' => '¿Dispone de colores corporativos definidos?',
                'choices' => [
                    'Sí' => true,
                    'No' => false,
                ],
                'expanded' => false,
                'multiple' => false,
                'data' => false,
            ])
            ->add('disponeRecursosDisenyo', ChoiceType::class, [
                'label' => '¿Dispone de fotografías, vídeos, diseño web…?',
                'choices' => [
                    'Sí' => true,
                    'No' => false,
                ],
                'expanded' => false,
                'multiple' => false, 
                'data' => false,
            ])

            // --------------------------
            ->add('mantenimiento', ChoiceType::class, [
                'choices' => [
                    'Sí' => true,
                    'No' => false,
                ],
                'expanded' => false,
                'multiple' => false, 
            ])
            ->add('dominio', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'style' => 'height: 150px',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, rellena este campo.'
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Este campo no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ])
            ->add('comentarios', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Aclaraciones o algo para comentar',
                    'style' => 'height: 250px',
                ],
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'Este campo no puede tener más de {{ limit }} caracteres.'
                    ])
                ]
            ])
            //->add('fecha_creacion_briefing_web')
            /*temporal*/
            /*->add('usuario', ChoiceType::class, [
                'choices' => $this->getUsuariosSinRolAdmin(),
                'choice_label' => 'username', 
            ])*/
            ->add('submit', SubmitType::class, [
                'label' => 'Enviar Briefing Web',
                'attr' => ['class' => 'btn custom-btn btn-lg btn-block'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BriefingWeb::class,
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
