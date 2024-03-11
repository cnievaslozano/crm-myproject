<?php

namespace App\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\BriefingLogo;
use App\Entity\Usuario;
use App\Repository\BriefingLogoRepository;
use App\Form\BriefingLogoType;
use Dompdf\Dompdf;

class BriefingLogoController extends AbstractController
{

    public function new(Request $request, EntityManagerInterface $em): Response
    {

        $user = $this->getUser();
        $empresa = $user->getEmpresa();

        $briefingLogo = $empresa->getBriefingLogo();
        $form = $this->createForm(BriefingLogoType::class, $briefingLogo);

        // Procesar el formulario si se ha enviado
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (!$briefingLogo->isActivo()) {
                    throw new \Exception('El briefing logo no lo tienes activo, porfavor contácta con un administrador.');
                }
                
                if ($briefingLogo->getFechaCreacionBriefingLogo()) {
                    throw new \Exception('Ya has enviado un briefing de logo anteriormente.');
                }

                // Asignar datos adicionales
                $briefingLogo->setFechaCreacionBriefingLogo(new \DateTime());
                $briefingLogo->setEstado("En Progreso");
                $briefingLogo->setActivo(true);

                // Persistir el briefinglogo en la base de datos
                $em->persist($briefingLogo);
                $em->flush();

                // Mostrar un mensaje de éxito
                $this->addFlash('success', 'El Briefing del Logo se ha enviado con éxito.');

                // Redirigir a una página de éxito o realizar otras acciones necesarias
                return $this->redirectToRoute('briefing_logo_new');
            } catch (\Exception $e) {
                // Capturar la excepción y mostrar un mensaje de error al usuario
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('formularios/logo.html.twig', [
            'username' => $user->getUsername(),
            'form' => $form->createView(),
        ]);
    }
    public function show(BriefingLogo $briefingLogo): Response
    {
        $empresa = $briefingLogo->getEmpresa();

        return $this->render('dashboard/briefinglogo/show.html.twig', [
            'empresa' => $empresa,
            'briefing_logo' => $briefingLogo,
        ]);
    }



    public function delete(Request $request, BriefingLogo $briefingLogo, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $briefingLogo->getId(), $request->request->get('_token'))) {
            $briefingLogo->setActivo(False);
            $briefingLogo->setEstado("");

            $em->persist($briefingLogo);
            $em->flush();
        }

        return $this->redirectToRoute('dashboard_briefings', [], Response::HTTP_SEE_OTHER);
    }
    public function descargarPDF($id): Response
    {
        // Obtiene el briefing app por su ID
        $briefingLogo = $this->getDoctrine()->getRepository(BriefingLogo::class)->find($id);
        $usuario = $briefingLogo->getUsuario();
        $empresa = $usuario->getEmpresa();

        $html =  $this->renderView('dashboard/briefinglogo/plantilla_pdf.html.twig', [
            'briefing_logo' => $briefingLogo,
            'empresa' => $empresa,
            'usuario' => $usuario,
        ]);

        // Crea una instancia de Dompdf
        $dompdf = new Dompdf();

        // Carga el HTML en Dompdf
        $dompdf->loadHtml($html);

        // Renderiza el PDF
        $dompdf->render();

        // Obtén el contenido del PDF
        $pdfContent = $dompdf->output();

        // Define el nombre del archivo PDF
        $filename = 'briefing_logo_' . $briefingLogo->getId() . '_' . $empresa->getNombre() . '.pdf';

        // Devuelve el PDF como una respuesta de Symfony para descargarlo
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        // Agrega un mensaje flash de éxito
        $this->addFlash('success', 'Briefing Logo descargado con éxito.');

        // Redirige a la página 'dashboard_briefings' después de un segundo
        $response->headers->add(['refresh' => '1;url=' . $this->generateUrl('dashboard_briefings')]);

        return $response;
    }
}
