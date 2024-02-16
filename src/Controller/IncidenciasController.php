<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Incidencia;
use App\Form\IncidenciaType;
use App\Repository\IncidenciaRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Service\FileUploader;
use Dompdf\Dompdf;


class IncidenciasController extends AbstractController
{
    public function index(Request $request, EntityManagerInterface $em, FileUploader $fileUploader): Response
    {
        $user = $this->getUser();
        $incidencia = new Incidencia();
        $form = $this->createForm(IncidenciaType::class, $incidencia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $rutaImagenesFile = $form['ruta_imagenes']->getData();
                if ($rutaImagenesFile) {
                    // Sube el archivo solo si se ha proporcionado
                    $rutaImagenesFileName = $fileUploader->upload($rutaImagenesFile);
                    // Establece el nombre del archivo subido en la entidad
                    $incidencia->setRutaImagenes($rutaImagenesFileName);
                }

                $tipo = $form['tipo']->getData();

                if ($tipo == 'Web') {
                    $briefingWeb = $user->getBriefingWeb();
                    $incidencia->setBriefingWeb($briefingWeb);
                } elseif ($tipo == 'App') {
                    $briefingApp = $user->getBriefingApp();
                    $incidencia->setBriefingApp($briefingApp);
                }

                if (!$briefingWeb && !$briefingApp) {
                    throw new \RuntimeException('No se encontró un briefing asociado al usuario.');
                }


                $incidencia->setFechaCreacionIncidencia(new \DateTime());

                $em->persist($incidencia);
                $em->flush();

                $this->addFlash('success', 'La incidencia se ha enviado con éxito.');
                return $this->redirectToRoute('dashboard_empresa');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde.');
                $this->addFlash('error', $e);
            }
        }

        return $this->render('formularios/incidencias.html.twig', [
            'username' => $user->getUsername(),
            'form' => $form->createView(),
        ]);
    }


    public function show(Incidencia $incidencia): Response
    {
        //obtener el briefing asociado a la incidencia
        $briefingAsociado = $incidencia->getBriefingApp() ?? $incidencia->getBriefingWeb();

        //obtenemos el usuario del briefing
        $usuario = $briefingAsociado->getUsuario();

        //obtener la empresa asociada a la incidencia
        $empresa = $usuario->getEmpresa();



        return $this->render('dashboard/incidencia/show.html.twig', [
            'empresa' => $empresa,
            'usuario' => $usuario,
            'briefing' => $briefingAsociado,
            'incidencia' => $incidencia
        ]);
    }

    public function delete(Request $request, Incidencia $incidencia, IncidenciaRepository $incidenciaRepository)
    {

        if ($this->isCsrfTokenValid('delete' . $incidencia->getId(), $request->request->get('_token'))) {
            $incidenciaRepository->remove($incidencia, true);
        }

        return $this->redirectToRoute('dashboard_incidencias', [], Response::HTTP_SEE_OTHER);
    }

    public function descargarPDF($id): Response
    {
        // Obtiene el briefing app por su ID
        $incidencia = $this->getDoctrine()->getRepository(Incidencia::class)->find($id);

        //obtenemos el usuario del briefing
        $briefingAsociado = $incidencia->getBriefingApp() ?? $incidencia->getBriefingWeb();
        $usuario = $briefingAsociado->getUsuario();
        $empresa = $usuario->getEmpresa();

        $html =  $this->renderView('dashboard/incidencia/plantilla_pdf.html.twig', [
            'incidencia' => $incidencia,
            'empresa' => $empresa,
            'briefing' => $briefingAsociado,
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
        $filename = 'incidencia' . $incidencia->getId() . '_' . $empresa->getNombre() . '.pdf';

        // Devuelve el PDF como una respuesta de Symfony para descargarlo
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        // Agrega un mensaje flash de éxito
        $this->addFlash('success', 'Incidencia descargada con éxito.');

        // Redirige a la página 'briefings_index' después de un segundo
        $response->headers->add(['refresh' => '1;url=' . $this->generateUrl('dashboard_incidencias')]);

        return $response;
        
    }
}
