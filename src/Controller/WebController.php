<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Web;
use App\Form\WebType;
use App\Entity\Empresa;

class WebController extends AbstractController
{
    public function new(Request $request, EntityManagerInterface $em, $id): Response
    {
        try {
            $empresa = $this->getDoctrine()->getRepository(Empresa::class)->find($id);

            if (!$empresa) {
                throw $this->createNotFoundException('Empresa no encontrada para el ID: ' . $id);
            }

            // Instancia del formulario
            $web = new Web();
            $form = $this->createForm(WebType::class, $web);

            // Procesar el formulario
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $web->setEmpresa($empresa);

                // persisitr la web en la bd
                $em->persist($web);
                $em->flush();

                // Mostrar un mensaje de éxito
                $this->addFlash('success', 'La web se ha registrado con éxito');

                // que vuelva al formulario o página de éxito
                return $this->redirectToRoute('empresa_show', ['id' => $empresa->getId()]);
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Se ha producido un error: ' . $e->getMessage());
        }


        // Si el formulario no se ha enviado o no es válido, vuelve a la página del formulario
        return $this->render('formularios/webEmpresa.html.twig', [
            'empresa' => $empresa,
            'form' => $form->createView(),
        ]);
    }
}
