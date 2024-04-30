<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Contacto;
use App\Form\ContactoType;
use App\Entity\Usuario;

class ContactoController extends AbstractController
{

    public function new(Request $request, EntityManagerInterface $em, $id): Response
    {
        try {
            $usuario = $this->getDoctrine()->getRepository(Usuario::class)->find($id);

            if (!$usuario) {
                throw $this->createNotFoundException('Usuario no encontrado para el ID: ' . $id);
            }

            // Crear una instancia del formulario
            $contacto = new Contacto();
            $form = $this->createForm(ContactoType::class, $contacto);

            // Procesar el formulario si se ha enviado
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $contacto->setUsuario($usuario);

                // Persistir el contacto en la base de datos
                $em->persist($contacto);
                $em->flush();

                // Mostrar un mensaje de éxito
                $this->addFlash('success', 'El contacto se ha registrado con éxito.');

                // Redirigir a una página de éxito o realizar otras acciones necesarias
                return $this->redirectToRoute('usuario_show', ['id' => $usuario->getId()]);

            }

            // Si el formulario no se ha enviado o no es válido, se renderiza la página del formulario
            return $this->render('formularios/contacto.html.twig', [
                'usuario' => $usuario,
                'form' => $form->createView(),
            ]);
        } catch (\Exception $e) {
            // Manejar la excepción aquí, por ejemplo, mostrar un mensaje de error
            $this->addFlash('error', 'Ha ocurrido un error: ' . $e->getMessage());

            // Redirigir a una página de error o realizar otras acciones necesarias
            return $this->redirectToRoute('contacto_new', ['id' => $usuario->getId()]);
        }
    }
}
