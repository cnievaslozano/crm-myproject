<?php

namespace App\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\BriefingWeb;
use App\Repository\BriefingWebRepository;
use App\Form\BriefingWebType;
use App\Entity\Usuario;
use Dompdf\Dompdf;

class BriefingWebController extends AbstractController
{

    public function new(Request $request, EntityManagerInterface $em): Response
    {

        $user = $this->getUser();
        $empresa = $user->getEmpresa();

        $briefingWeb = $empresa->getBriefingWeb();
        $form = $this->createForm(BriefingWebType::class, $briefingWeb);

        // Procesar el formulario si se ha enviado
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (!$briefingWeb->isActivo()) {
                    throw new \Exception('El briefing web no lo tienes activo, porfavor contácta con un administrador.');
                }

                if ($briefingWeb->getFechaCreacionBriefingWeb()) {
                    throw new \Exception('Ya has enviado un briefing de web anteriormente.');
                }

                
                // Asignar datos adicionales
                $briefingWeb->setFechaCreacionBriefingWeb(new \DateTime());
                $briefingWeb->setEstado("En Progreso");
                $briefingWeb->setActivo(true);

                // Persistir el briefingweb en la base de datos
                $em->persist($briefingWeb);
                $em->flush();

                // Mostrar un mensaje de éxito
                $this->addFlash('success', 'El Briefing Web se ha enviado con éxito.');

                // Redirigir a una página de éxito o realizar otras acciones necesarias
                return $this->redirectToRoute('briefing_web_new');
            } catch (\Exception $e) {
                // Manejar otras excepciones
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('formularios/web.html.twig', [
            'username' => $user->getUsername(),
            'form' => $form->createView(),
        ]);
    }

    public function show(BriefingWeb $briefingWeb): Response
    {
        // Obtener el usuario asociado al briefing
        $empresa = $briefingWeb->getEmpresa();

        return $this->render('dashboard/briefingweb/show.html.twig', [
            'empresa' => $empresa,
            'briefing_web' => $briefingWeb,
        ]);
    }


    public function delete(Request $request, BriefingWeb $briefingWeb, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $briefingWeb->getId(), $request->request->get('_token'))) {
            $briefingWeb->setActivo(False);
            $briefingWeb->setEstado("");

            
            $em->persist($briefingWeb);
            $em->flush();
         }

        return $this->redirectToRoute('dashboard_briefings');
    }

    public function descargarPDF($id): Response
    {
        // Obtiene el briefing app por su ID
        $briefingWeb = $this->getDoctrine()->getRepository(BriefingWeb::class)->find($id);
        $usuario = $briefingWeb->getUsuario();
        $empresa = $usuario->getEmpresa();

        $html =  $this->renderView('dashboard/briefingweb/plantilla_pdf.html.twig', [
            'briefing_web' => $briefingWeb,
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
        $filename = 'briefing_web_' . $briefingWeb->getId() . '_' . $empresa->getNombre() . '.pdf';

        // Devuelve el PDF como una respuesta de Symfony para descargarlo
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        // Agrega un mensaje flash de éxito
        $this->addFlash('success', 'Briefing Web descargado con éxito.');

        // Redirige a la página 'dashboard_briefings' después de un segundo
        $response->headers->add(['refresh' => '1;url=' . $this->generateUrl('dashboard_briefings')]);

        return $response;
    }
}
