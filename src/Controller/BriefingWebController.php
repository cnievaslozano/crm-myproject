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

    public function index(Request $request, EntityManagerInterface $em): Response
    {

        $user = $this->getUser();

        // Crear una instancia del formulario
        $briefingWeb = new BriefingWeb();
        $form = $this->createForm(BriefingWebType::class, $briefingWeb);

        // Procesar el formulario si se ha enviado
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Asignar datos adicionales
                $this->assignAdditionalData($briefingWeb, $user);

                // Persistir el briefingweb en la base de datos
                $em->persist($briefingWeb);
                $em->flush();

                // Mostrar un mensaje de éxito
                $this->addFlash('success', 'El Briefing Web se ha enviado con éxito.');

                // Redirigir a una página de éxito o realizar otras acciones necesarias
                return $this->redirectToRoute('briefingweb');
            } catch (UniqueConstraintViolationException $e) {
                // Capturar la excepción de violación de la restricción única
                // y mostrar un mensaje de error al usuario
                $this->addFlash('error', 'Ya has enviado un briefing web anteriormente.');
            } catch (\Exception $e) {
                // Manejar otras excepciones
                $this->addFlash('error', 'Ha ocurrido un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde.');
            }
        }

        return $this->render('formularios/web.html.twig', [
            'username' => $user->getUsername(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Asigna datos adicionales al briefing de la aplicación.
     *
     * @param BriefingWeb $briefingWeb El briefing de la aplicación.
     * @param Uusario $user El usuario asociado al briefing.
     */
    private function assignAdditionalData(BriefingWeb $briefingWeb, Usuario $user): void
    {
        $briefingWeb->setFechaCreacionBriefingWeb(new \DateTime());
        $briefingWeb->setUsuario($user);
        $briefingWeb->setActivo(true);
    }

    public function show(BriefingWeb $briefingWeb): Response
    {
        // Obtener el usuario asociado al briefing
        $usuario = $briefingWeb->getUsuario();
        $empresa = $usuario->getEmpresa();

        return $this->render('dashboard/briefingweb/show.html.twig', [
            'empresa' => $empresa,
            'usuario' => $usuario,
            'briefing_web' => $briefingWeb,
        ]);
    }

    /* function edit */

    public function delete(Request $request, BriefingWeb $briefingWeb, BriefingWebRepository $briefingWebRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $briefingWeb->getId(), $request->request->get('_token'))) {
            $briefingWebRepository->remove($briefingWeb, true);
        }

        return $this->redirectToRoute('app_briefing_web_crud_index', [], Response::HTTP_SEE_OTHER);
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

        // Redirige a la página 'briefings_index' después de un segundo
        $response->headers->add(['refresh' => '1;url=' . $this->generateUrl('briefings_index')]);

        return $response;
    }
}
