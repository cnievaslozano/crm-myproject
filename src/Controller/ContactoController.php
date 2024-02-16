<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Contacto;
use App\Form\ContactoType;
class ContactoController extends AbstractController
{

    public function index(Request $request, EntityManagerInterface $em): Response
    {
        // Crear una instancia del formulario
        $contacto = new Contacto();
        $form = $this->createForm(ContactoType::class, $contacto);

        // Procesar el formulario si se ha enviado
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Persistir el contacto en la base de datos
            $em->persist($contacto);
            $em->flush();

            // Mostrar un mensaje de éxito
            $this->addFlash('success', 'El contacto se ha registrado con éxito.');

            // Redirigir a una página de éxito o realizar otras acciones necesarias
            return $this->render('contacto/index.html.twig', [
                'controller_name' => 'ContactoController',
                'form' => $form->createView(),
            ]);
        }

        // Si el formulario no se ha enviado o no es válido, se renderiza la página del formulario
        return $this->render('contacto/index.html.twig', [
            'controller_name' => 'ContactoController',
            'form' => $form->createView(),
        ]);
    }
}
