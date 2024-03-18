<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\BriefingApp;
use App\Entity\Usuario;
use App\Repository\BriefingAppRepository;
use App\Form\BriefingAppType;
use App\Service\MisFunciones;
use Dompdf\Dompdf;
use Symfony\Component\String\Slugger\SluggerInterface;

class BriefingAppController extends AbstractController
{

    public function new(Request $request, MisFunciones $misFunciones, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $empresa = $user->getEmpresa();

        $briefingApp = $empresa->getBriefingApp();
        $form = $this->createForm(BriefingAppType::class, $briefingApp);

        // Procesar el formulario si se ha enviado
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (!$briefingApp->getFechaCreacionBriefingApp()) {
                    throw new \Exception('El briefing app no lo tienes activo, porfavor contácta con un administrador.');
                }

                if ($briefingApp->isActivo()) {
                    throw new \Exception('Ya has enviado un briefing de app anteriormente.');
                }

                // Procesar la imagen
                $brochureFile = $form['imagen_logotipo_ruta']->getData();

                // Validar si el archivo es una imagen
                if ($brochureFile !== null) {
                    if (!$misFunciones->validateImage($brochureFile)) {
                        $this->addFlash('error', 'El archivo no es una imagen válida.');
                        return $this->redirectToRoute('briefing_app_new');
                    }
                    $misFunciones->processImage($briefingApp, "briefing_app_new", "logotipos_directory", $slugger, $brochureFile);
                }

                // Asignar datos adicionales
                $briefingApp->setFechaCreacionBriefingApp(new \DateTime());
                $briefingApp->setActivo(true);
                $briefingApp->setEstado("En Progreso");


                // Persistir el briefingapp en la base de datos
                $em->persist($briefingApp);
                $em->flush();

                // Mostrar un mensaje de éxito
                $this->addFlash('success', 'El Briefing de la App se ha enviado con éxito.');

                // Redirigir a la misma página
                return $this->redirectToRoute('briefing_app_new');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('formularios/app.html.twig', [
            'username' => $user->getUsername(),
            'form' => $form->createView(),
        ]);
    }

    public function show(BriefingApp $briefingApp): Response
    {
        $empresa = $briefingApp->getEmpresa();

        return $this->render('dashboard/briefingapp/show.html.twig', [
            'empresa' => $empresa,
            'briefing_app' => $briefingApp,
        ]);
    }

    public function delete(Request $request, BriefingApp $briefingApp, EntityManagerInterface $em, SessionInterface $session): Response
    {
        $empresa = $briefingApp->getEmpresa();

        if ($this->isCsrfTokenValid('delete' . $briefingApp->getId(), $request->request->get('_token'))) {
            $briefingApp->setActivo(False);
            $briefingApp->setEstado("");

            $this->addFlash('success', 'se ha eliminado éxitosamente el briefing app de ' . $empresa->getNombre());
            $em->persist($briefingApp);
            $em->flush();
        }

        // Guardar la página desde la que se elimina el briefing en una variable de sesión
        $referer = $request->headers->get('referer');
        if (strpos($referer, $this->generateUrl('dashboard_briefings')) !== false) {
            $session->set('delete_referer', 'dashboard_briefings');
        } else {
            $session->set('delete_referer', 'empresa_show');
        }

        // Obtener la página de redirección desde la variable de sesión
        $redirectRoute = $session->get('delete_referer');

        return $this->redirectToRoute($redirectRoute, ['id' => $empresa->getId()]);
    }
    public function descargarPDF($id): Response
    {
        // Obtiene el briefing app por su ID
        $briefingApp = $this->getDoctrine()->getRepository(BriefingApp::class)->find($id);
        $usuario = $briefingApp->getUsuario();
        $empresa = $usuario->getEmpresa();

        $html =  $this->renderView('dashboard/briefingapp/plantilla_pdf.html.twig', [
            'briefing_app' => $briefingApp,
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
        $filename = 'briefing_app_' . $briefingApp->getId() . '_' . $empresa->getNombre() . '.pdf';

        // Devuelve el PDF como una respuesta de Symfony para descargarlo
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        // Agrega un mensaje flash de éxito
        $this->addFlash('success', 'Briefing App descargado con éxito.');

        // Redirige a la página 'dashboard_briefings' después de un segundo
        $response->headers->add(['refresh' => '1;url=' . $this->generateUrl('dashboard_briefings')]);

        return $response;
    }
}
