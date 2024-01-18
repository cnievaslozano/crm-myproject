<?php

namespace App\Controller;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UsuarioType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistroController extends AbstractController
{
    private function checkAdminRole(): bool
    {
        $user = $this->getUser();

        if (!$user) {
            // Si no hay usuario autenticado, redirige a la página de login
            $this->redirectToRoute('app_login');
            return false;
        } else {
            $roles = $user->getRoles();
        }
        

        return in_array('ROLE_ADMIN', $roles, true);
    }
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response 
    {
        if ($this->checkAdminRole()) {
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
    
                // atributos default
                // Verificar si el ID del usuario es 1 para asignar el rol "ADMIN"
                if ($user->getId() === 1) {
                    $user->setRoles(['ROLE_ADMIN']);
                } else {
                    $user->setRoles(['ROLE_USER']);
                }
                $user->setFechaCreacionUsuario(new \DateTime());
                $user->setActivo(true);
    
                $em->persist($user);
                $em->flush();
                $this->addFlash('exito', 'Se ha registrado bien');
                return $this->redirectToRoute('usuario_registro');
            }
    
            return $this->render('registro/index.html.twig', [
                'nombre_empresa' => 'ejemplo',
                'controller_name' => 'RegistroController',
                'form' => $form->createView(),
            ]);
        } else {
            // Si el usuario no tiene el rol de administrador, redirige a la página de login
            return $this->redirectToRoute('app_login');
        }
    }
    
}
