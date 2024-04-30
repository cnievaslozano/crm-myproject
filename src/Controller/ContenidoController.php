<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Contenido;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Form\ContenidoType;
use App\Repository\ContenidoRepository;
use App\Entity\Usuario;
use Symfony\Component\String\Slugger\SluggerInterface;
use Dompdf\Dompdf;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\MisFunciones;

class ContenidoController extends AbstractController
{
    public function new(Request $request, MisFunciones $misFunciones, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        try {
            // Crear una instancia del formulario
            $contenido = new Contenido();
            $user = $this->getUser();
            $empresa = $user->getEmpresa();
            $briefingweb = $empresa->getBriefingWeb();
            $form = $this->createForm(ContenidoType::class, $contenido);

            // Procesar el formulario si se ha enviado
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Procesar la imagen del contenido
                $brochureFile = $form['ruta_imagenes_contenidos']->getData();

                // Validar si el archivo es una imagen
                if ($brochureFile !== null) {
                    // Validar si el archivo es una imagen
                    if (!$misFunciones->validateImage($brochureFile)) {
                        $this->addFlash('error', 'El archivo no es una imagen válida.');
                        return $this->redirectToRoute('contenido_new');
                    }
                    $misFunciones->processImage($contenido, "contenido_new", "contenidos_directory", $slugger, $brochureFile);
                }

                // Establecer el resto de los campos del contenido
                $contenido->setFechaCreacionContenido(new \DateTime())
                    ->setActivo(true)
                    ->setBriefingWeb($briefingweb);

                // Verificar si el usuario tiene un briefing web asociado
                if (!$briefingweb->isActivo()) {
                    throw new \Exception('No tienes un briefing web activo, no puedes solicitar un contenido. Por favor, contacta con el administrador.');
                }

                // Persistir el contenido en la base de datos
                $em->persist($contenido);
                $em->flush();

                // Mostrar un mensaje de éxito y redirigir
                $this->addFlash('success', 'El Contenido se ha enviado con éxito.');
                return $this->redirectToRoute('dashboard_empresa');
            }

            // Renderizar el formulario
            return $this->render('formularios/contenido.html.twig', [
                'username' => $user->getUsername(),
                'form' => $form->createView(),
            ]);
        } catch (\Exception $e) {
            // Manejar la excepción aquí, por ejemplo, mostrar un mensaje de error
            $this->addFlash('error', 'Ha ocurrido un error: ' . $e->getMessage());

            // Redirigir a una página de error o realizar otras acciones necesarias
            return $this->redirectToRoute('dashboard_empresa');
        }
    }


    public function show(Contenido $contenido, ContenidoRepository $contenidoRepository): Response
    {

        $briefingweb = $contenido->getBriefingWeb();
        $empresa = $briefingweb->getEmpresa();


        return $this->render('dashboard/contenido/show.html.twig', [
            'empresa' => $empresa,
            'briefingweb' => $briefingweb,
            'contenido' => $contenido,
        ]);
    }


    public function delete(Request $request, Contenido $contenido, ContenidoRepository $contenidoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contenido->getId(), $request->request->get('_token'))) {
            $contenidoRepository->remove($contenido, true);
        }

        return $this->redirectToRoute('dashboard_contenidos', [], Response::HTTP_SEE_OTHER);
    }

    public function descargarPDF($id): Response
    {
        // Obtiene el contenido por su ID
        $contenido = $this->getDoctrine()->getRepository(Contenido::class)->find($id);
        $briefingweb = $contenido->getBriefingWeb();
        $usuario = $briefingweb->getUsuario();
        $empresa = $usuario->getEmpresa();
        $briefingweb = $contenido->getBriefingWeb();

        $html =  $this->renderView('dashboard/contenido/plantilla_pdf.html.twig', [
            'empresa' => $empresa,
            'usuario' => $usuario,
            'briefingweb' => $briefingweb,
            'contenido' => $contenido,
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
        $filename = 'contenido_' . $contenido->getId() . '_' . $empresa->getNombre() . '.pdf';

        // Devuelve el PDF como una respuesta de Symfony para descargarlo
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        // Agrega un mensaje flash de éxito
        $this->addFlash('success', 'Contenido descargado con éxito.');

        // Redirige a la página '' después de un segundo
        $response->headers->add(['refresh' => '1;url=' . $this->generateUrl('dashboard_contenidos')]);

        return $response;
    }
}
