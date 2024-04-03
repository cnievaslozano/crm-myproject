<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
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


    public function delete(Request $request, BriefingWeb $briefingWeb, EntityManagerInterface $em, SessionInterface $session): Response
    {
        $empresa = $briefingWeb->getEmpresa();

        if ($this->isCsrfTokenValid('delete' . $briefingWeb->getId(), $request->request->get('_token'))) {
            $briefingWeb->setActivo(False);
            $briefingWeb->setEstado("");

            $this->addFlash('success', 'se ha eliminado éxitosamente el briefing web de ' . $empresa->getNombre());
            $em->persist($briefingWeb);
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

}
