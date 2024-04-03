<?php

namespace App\Service;

use App\Entity\BriefingWeb;
use App\Entity\BriefingApp;
use App\Entity\BriefingLogo;
use App\Entity\Contenido;
use App\Entity\Empresa;
use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;


class MisFunciones extends AbstractController
{
    /**
     * Procesa la imagen asociada a una entidad.
     *
     * @param mixed $entidad La entidad a la que se asociará la imagen.
     * @param string $redirectRoute La ruta de redireccionamiento en caso de error.
     * @param string $fileDirectory El directorio donde se almacenará el archivo.
     * @param SluggerInterface $slugger El servicio Slugger para manejar nombres de archivo seguros.
     * @param UploadedFile|null $brochureFile El archivo de imagen a procesar.
     * @return RedirectResponse|null Retorna una redirección en caso de error o NULL si no hay errores.
     */


    public function processImage($entidad, string $redirectRoute, string $fileDirectory, SluggerInterface $slugger, $brochureFile)
    {
        if ($brochureFile) {
            $newFilename = $this->uploadFile($brochureFile, $slugger, $fileDirectory);
            if (!$newFilename) {
                $this->addFlash('error', 'Ha ocurrido un error al procesar la imagen.');
                return $this->redirectToRoute($redirectRoute);
            }

            // Guarda la imagen
            $entidad->setImagen($newFilename);
        }
    }

    /**
     * Valida si el archivo es una imagen.
     *
     * @param UploadedFile $file El archivo a validar.
     * @return bool true si el archivo es una imagen, de lo contrario false.
     */
    public function validateImage(UploadedFile $file): bool
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $fileExtension = $file->guessExtension();
        return in_array(strtolower($fileExtension), $allowedExtensions);
    }

    /**
     * Carga un archivo en el servidor.
     *
     * @param UploadedFile $file El archivo a cargar.
     * @param SluggerInterface $slugger El servicio Slugger para generar nombres de archivo seguros.
     * @param string $fileDirectory El directorio donde se almacenará el archivo.
     * @return string|bool El nuevo nombre de archivo si la carga es exitosa, de lo contrario false.
     */

    private function uploadFile(UploadedFile $file, SluggerInterface $slugger, string $fileDirectory)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move(
                $this->getParameter($fileDirectory),
                $newFilename
            );
            return $newFilename;
        } catch (FileException $e) {
            return false;
        }
    }

    public function descargarPDF (Request $request, $id): Response {
        
        // Obtengo datos necesarios para el pdf
        $ruta = $request->get('_route');
        $tipo = explode('_', $ruta)[1];
        
        $tiposEntidad = [
            'web' => BriefingWeb::class,
            'logo' => BriefingLogo::class,
            'app' => BriefingApp::class
        ];
        
        $claseEntidad = $tiposEntidad[$tipo];
        $briefing = $this->getDoctrine()->getRepository($claseEntidad)->find($id);
        $empresa = $briefing->getEmpresa();

        // Renderizo la plantilla del pdf
        $html =  $this->renderView('dashboard/briefing'.$tipo.'/briefing'.$tipo.'-pdf.html.twig', [
            'briefing_'.$tipo => $briefing,
            'empresa' => $empresa,
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
        $filename = 'briefing_'.$tipo.'_' . $briefing->getId() . '_' . $empresa->getNombre() . '.pdf';

        // Devuelve el PDF como una respuesta de Symfony para descargarlo
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        // Agrega un mensaje flash de éxito
        $this->addFlash('success', 'Briefing ' . $tipo . ' descargado con éxito.');

         // Redirige a la página 'dashboard_briefings' después de un segundo
         $response->headers->add(['refresh' => '1;url=' . $this->generateUrl('dashboard_briefings')]);

         return $response;

    }
}
