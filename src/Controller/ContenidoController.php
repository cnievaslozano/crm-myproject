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

class ContenidoController extends AbstractController
{
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();

        // crear una instancia del formulario
        $contenido = new Contenido();
        $form = $this->createForm(ContenidoType::class, $contenido);

        // Procesar el formulario si se ha enviado
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Procesar la imagen del contenido
            $brochureFile = $form['ruta_imagenes_contenidos']->getData();
            $this->processImage($contenido, $slugger, $brochureFile);

            // Establecer el resto de los campos del contenido
            $this->assignAdditionalData($contenido, $user);

            // Persistir el contenido en la base de datos
            $em->persist($contenido);
            $em->flush();

            // Mostrar un mensaje de éxito y redirigir
            $this->addFlash('success', 'El Contenido se ha enviado con éxito.');
            return $this->redirectToRoute('contenido_new');
        }

        // Renderizar el formulario
        return $this->render('formularios/contenido.html.twig', [
            'username' => $user->getUsername(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Procesa la imagen asociada al contenido.
     *
     * @param Contenido $contenido EL contenido del briefing web.
     * @param SluggerInterface $slugger El servicio Slugger para manejar nombres de archivo seguros.
     * @throws FileException Si ocurre un error al procesar la imagen.
     */
    private function processImage(Contenido $contenido, SluggerInterface $slugger, $brochureFile)
    {
        if ($brochureFile) {
            $newFilename = $this->uploadFile($brochureFile, $slugger);
            if (!$newFilename) {
                $this->addFlash('error', 'Ha ocurrido un error al procesar la imagen.');
                return $this->redirectToRoute('contenido_new');
            }

            // Guarda la imagen
            $contenido->setRutaImagenesContenidos($newFilename);
        }
    }

    /**
     * Asigna datos adicionales al Contenido y comprueba si existe briefing web
     *
     * @param Contenido $contenido El contenido de la web.
     * @param Usuario $user El usuario que ha creado el contenido.
     */
    private function assignAdditionalData(Contenido $contenido, Usuario $user)
    {
        $contenido->setFechaCreacionContenido(new \DateTime())
            ->setActivo(true)
            ->setBriefingWeb($user->getBriefingWeb());

        // Verificar si el usuario tiene un briefing web asociado
        if (null === $user->getBriefingWeb()) {
            $this->addFlash('error', 'No tienes un briefing web asociado. Por favor, contacta con el administrador.');
            return $this->redirectToRoute('contenido_new');
        }
    }

    /**
     * Método para cargar un archivo.
     *
     * @param $file El archivo a cargar
     * @param SluggerInterface $slugger El servicio Slugger para generar nombres de archivo seguros
     *
     * @return string|bool El nuevo nombre de archivo si la carga es exitosa, de lo contrario false
     */

    private function uploadFile(UploadedFile $file, SluggerInterface $slugger)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move(
                $this->getParameter('contenidos_directory'),
                $newFilename
            );
            return $newFilename;
        } catch (FileException $e) {
            return false;
        }
    }


    public function show(Contenido $contenido, ContenidoRepository $contenidoRepository): Response
    {

        $briefingweb = $contenido->getBriefingWeb();

        // Inicializar la variable de usuario
        $usuario = null;

        // Verificar si el contenido tiene un briefing web asociado
        if ($briefingweb !== null) {
            // Obtener el usuario asociado al briefing web
            $usuario = $briefingweb->getUsuario();
            $empresa = $usuario->getEmpresa();
        }

        return $this->render('dashboard/contenido/show.html.twig', [
            'empresa' => $empresa,
            'usuario' => $usuario,
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
