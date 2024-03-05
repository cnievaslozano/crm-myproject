<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UsuarioType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Entity\Empresa;

class UserController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('login/index.html.twig');
    }

    public function new(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, $id): Response
    {
        $user = new Usuario();
        $empresa = $this->getDoctrine()->getRepository(Empresa::class)->find($id);

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
            $user->setEmpresa($empresa);

            // Verificar si el ID del usuario es 1 para asignar el rol "ADMIN"
            $user->setRoles($user->getId() === 1 ? ['ROLE_ADMIN'] : ['ROLE_USER']);
            $user->setFechaCreacionUsuario(new \DateTime());
            $user->setActivo(true);

            try {
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'Se ha registrado correctamente.');
                return $this->redirectToRoute('usuario_new', ['id' => $id]);
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'El nombre de usuario ya está en uso. Por favor, elige otro.');
            } catch (\Exception $e) {
                // Otra excepción no esperada
                $this->addFlash('error', $e);
                // Puedes agregar un registro de error detallado aquí si es necesario
                // return $this->redirectToRoute('ruta_donde_quieres_redirigir');
            }
        }

        return $this->render('formularios/usuario.html.twig', [
            'empresa' => $empresa,
            'form' => $form->createView(),
        ]);
    }

    public function show(Usuario $usuario): Response
    {
        $empresa = $usuario->getEmpresa();

        return $this->render('dashboard/usuario/show.html.twig', [
            'usuario' => $usuario,
            'empresa' => $empresa,
        ]);
    }
}
