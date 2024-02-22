<?php

namespace App\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\BriefingApp;
use App\Entity\Usuario;
use App\Repository\BriefingAppRepository;
use App\Form\BriefingAppType;
use Dompdf\Dompdf;
use Symfony\Component\String\Slugger\SluggerInterface;

class BriefingAppController extends AbstractController
{

    public function index(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();

        // Crear una instancia del formulario
        $briefingApp = new BriefingApp();
        $form = $this->createForm(BriefingAppType::class, $briefingApp);

        // Procesar el formulario si se ha enviado
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Procesar la imagen
                $brochureFile = $form['imagen_logotipo_ruta']->getData();
                $this->processImage($briefingApp, $slugger, $brochureFile);

                // Asignar datos adicionales
                $this->assignAdditionalData($briefingApp, $user);

                // Persistir el briefingapp en la base de datos
                $em->persist($briefingApp);
                $em->flush();

                // Mostrar un mensaje de éxito
                $this->addFlash('success', 'El Briefing de la App se ha enviado con éxito.');

                // Redirigir a la misma página
                return $this->redirectToRoute('briefingapp');
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'Ya has enviado un briefing de app anteriormente.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde.');
            }
        }

        return $this->render('formularios/app.html.twig', [
            'username' => $user->getUsername(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Procesa la imagen asociada al briefing de la aplicación.
     *
     * @param BriefingApp $briefingApp El briefing de la aplicación.
     * @param SluggerInterface $slugger El servicio Slugger para manejar nombres de archivo seguros.
     * @throws FileException Si ocurre un error al procesar la imagen.
     */
    private function processImage(BriefingApp $briefingApp, SluggerInterface $slugger, $brochureFile)
    {
        if ($brochureFile) {
            $newFilename = $this->uploadFile($brochureFile, $slugger);

            if (!$newFilename) {
                $this->addFlash('error', 'Ha ocurrido un error al procesar la imagen.');
                return $this->redirectToRoute('briefingapp');
            }

            // Actualizar la propiedad 'imagenLogotipoRuta'
            $briefingApp->setImagenLogotipoRuta($newFilename);
        }
    }

    /**
     * Asigna datos adicionales al briefing de la aplicación.
     *
     * @param BriefingApp $briefingApp El briefing de la aplicación.
     * @param Usuario $user El usuario asociado al briefing.
     */
    private function assignAdditionalData(BriefingApp $briefingApp, Usuario $user): void
    {
        $briefingApp->setUsuario($user);
        $briefingApp->setFechaCreacionBriefingApp(new \DateTime());
        $briefingApp->setActivo(true);
    }

    /**
     * Método para cargar un archivo.
     *
     * @param $file El archivo a subir
     * @param SluggerInterface $slugger El servicio slugger para generar nombres de archivo seguros
     *
     * @return string|bool El nuevo nombre de archivo si la carga es exitosa, de lo contrario false
     */


    private function uploadFile($file, SluggerInterface $slugger)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move(
                $this->getParameter('logotipos_directory'),
                $newFilename
            );
            return $newFilename;
        } catch (FileException $e) {
            return false;
        }
    }

    public function show(BriefingApp $briefingApp): Response
    {
        // Obtener el usuario asociado al briefing
        $usuario = $briefingApp->getUsuario();
        $empresa = $usuario->getEmpresa();

        return $this->render('dashboard/briefingapp/show.html.twig', [
            'empresa' => $empresa,
            'usuario' => $usuario,
            'briefing_app' => $briefingApp,
        ]);
    }



    public function delete(Request $request, BriefingApp $briefingApp, BriefingAppRepository $briefingAppRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $briefingApp->getId(), $request->request->get('_token'))) {
            $briefingAppRepository->remove($briefingApp, true);
        }

        return $this->redirectToRoute('app_briefing_app_crud_index', [], Response::HTTP_SEE_OTHER);
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

        // Redirige a la página 'briefings_index' después de un segundo
        $response->headers->add(['refresh' => '1;url=' . $this->generateUrl('briefings_index')]);

        return $response;
    }
}
