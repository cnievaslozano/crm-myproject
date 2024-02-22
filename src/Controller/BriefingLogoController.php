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

        // Crear una instancia del formulario
        $briefingLogo = new BriefingLogo();
        $form = $this->createForm(BriefingLogoType::class, $briefingLogo);

        // Procesar el formulario si se ha enviado
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                // Asignar datos adicionales
                $this->assignAdditionalData($briefingLogo, $user);

                // Persistir el briefinglogo en la base de datos
                $em->persist($briefingLogo);
                $em->flush();

                // Mostrar un mensaje de éxito
                $this->addFlash('success', 'El Briefing del Logo se ha enviado con éxito.');

                // Redirigir a una página de éxito o realizar otras acciones necesarias
                return $this->redirectToRoute('briefing_logo_new');
            } catch (UniqueConstraintViolationException $e) {
                // Capturar la excepción de violación de la restricción única
                // y mostrar un mensaje de error al usuario
                $this->addFlash('error', 'Ya has enviado un briefing de logo anteriormente.');
            } catch (\Exception $e) {
                // Manejar otras excepciones
                $this->addFlash('error', 'Ha ocurrido un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde.');
            }
        }

        return $this->render('formularios/logo.html.twig', [
            'username' => $user->getUsername(),
            'form' => $form->createView(),
        ]);
    }
    /**
     * Asigna datos adicionales al briefing de la aplicación.
     *
     * @param BriefingLogo $briefingLogo El briefing de la aplicación.
     * @param UserInterface $user El usuario asociado al briefing.
     */
    private function assignAdditionalData(BriefingLogo $briefingLogo, Usuario $user): void
    {
        // Datos que necesita la empresa que no se rellenan en el formulario
        $briefingLogo->setFechaCreacionBriefingLogo(new \DateTime());
        $briefingLogo->setUsuario($user);
        $briefingLogo->setActivo(true);
    }

    public function show(BriefingLogo $briefingLogo): Response
    {
        // Obtener el usuario asociado al briefing
        $usuario = $briefingLogo->getUsuario();
        $empresa = $usuario->getEmpresa();

        return $this->render('dashboard/briefinglogo/show.html.twig', [
            'empresa' => $empresa,
            'usuario' => $usuario,
            'briefing_logo' => $briefingLogo,
        ]);
    }

    

    public function delete(Request $request, BriefingLogo $briefingApp, BriefingLogoRepository $briefingLogoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $briefingApp->getId(), $request->request->get('_token'))) {
            $briefingLogoRepository->remove($briefingApp, true);
        }

        return $this->redirectToRoute('app_briefing_logo_crud_index', [], Response::HTTP_SEE_OTHER);
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
