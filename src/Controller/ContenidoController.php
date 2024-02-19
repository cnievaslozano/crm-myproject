<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Contenido;
use App\Entity\Usuario;
use App\Form\ContenidoType;
use App\Repository\ContenidoRepository;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;

class ContenidoController extends AbstractController
{
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $nombreusuario = $user->getUsername();

        // Crear instancia del formulario
        $contenido = new Contenido();
        $form = $this->createForm(ContenidoType::class, $contenido);

        // Procesar el formulario si se ha enviado
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Datos que necesita el contenido que no se rellenan en el formulario
                $contenido->setFechaCreacionContenido(new \DateTime());
                $contenido->setActivo(true);
                $briefingWeb = $user->getBriefingWeb();
                $contenido->setBriefingWeb($briefingWeb);

                if (!$briefingWeb) {
                    $this->addFlash('error', 'No tienes un briefing web asociado. Por favor, contacta con el administrador.');
                    return $this->redirectToRoute('registro_contenido');
                }

                // Persistir el briefingweb en la base de datos
                $em->persist($contenido);
                $em->flush();

                // Mostrar un mensaje de éxito
                $this->addFlash('success', 'El Contenido se ha enviado con éxito.');

                // Redirigir a una página de éxito o realizar otras acciones necesarias
                return $this->redirectToRoute('registro_contenido');
            } catch (\Exception $e) {
                if ($e->getMessage() === 'No existe briefing web asociado al usuario.') {
                    $this->addFlash('error', 'No tienes un briefing web asociado. Por favor, contacta con el administrador.');
                } else {
                    $this->addFlash('error', 'Ha ocurrido un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde.');
                }
            }
        }

        return $this->render('formularios/contenido.html.twig', [
            'username' => $nombreusuario,
            'form' => $form->createView(),
        ]);
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
        $this->addFlash('success', 'Briefing App descargado con éxito.');

        // Redirige a la página '' después de un segundo
        $response->headers->add(['refresh' => '1;url=' . $this->generateUrl('dashboard_contenidos')]);

        return $response;
    }
}
