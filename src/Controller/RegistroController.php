<?php

namespace App\Controller;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UsuarioType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class RegistroController extends AbstractController
{
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response 
    {
        $user = new Usuario();
        $form = $this->createForm(UsuarioType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) { 
            $user = $form->getData();
            $textoplanoPassword = $user->getPassword();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $textoplanoPassword
            );
            $user->setPassword($hashedPassword);
    
            // Verificar si el ID del usuario es 1 para asignar el rol "ADMIN"
            $user->setRoles($user->getId() === 1 ? ['ROLE_ADMIN'] : ['ROLE_USER']);
            $user->setFechaCreacionUsuario(new \DateTime());
            $user->setActivo(true);
    
            try {
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'Se ha registrado correctamente.');
                return $this->redirectToRoute('registro_usuario');
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'El nombre de usuario ya está en uso. Por favor, elige otro.');
            } catch (\Exception $e) {
                // Otra excepción no esperada
                $this->addFlash('error', 'Ha ocurrido un error al registrar el usuario.');
                // Puedes agregar un registro de error detallado aquí si es necesario
                // return $this->redirectToRoute('ruta_donde_quieres_redirigir');
            }
        }
    
        return $this->render('registro/index.html.twig', [
            'nombre_empresa' => 'ejemplo',
            'controller_name' => 'RegistroController',
            'form' => $form->createView(),
        ]);
    }
    

    
}
