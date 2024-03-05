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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Repository\EmpresaRepository;

class UsuarioType extends AbstractType
{
    private $empresaRepository;

    public function __construct(EmpresaRepository $empresaRepository)
    {
        $this->empresaRepository = $empresaRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $empresas = $this->empresaRepository->findAll(); // Obtener todas las empresas de la base de datos

        // Transformar los datos de las empresas en el formato necesario para las opciones del desplegable
        $choices = [];
        foreach ($empresas as $empresa) {
            // Utilizar la entidad completa como valor y nombre de opción
            $choices[$empresa->getNombre()] = $empresa;
        }

        $builder
            ->add('username', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg'],
            ])
            ->add('funcion', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg'],
            ])
            ->add('nombre_usuario', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg'],
            ])
            ->add('apellidos_usuario', TextType::class, [
                'attr' => ['class' => 'form-control form-control-lg'],
            ])

            ->add('password', PasswordType::class, [
                'attr' => ['class' => 'form-control form-control-lg'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Crear Usuario',
                'attr' => ['class' => 'btn btn-granota btn-lg btn-block'],
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
