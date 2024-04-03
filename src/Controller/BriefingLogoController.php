<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
        $empresa = $user->getEmpresa();

        $briefingLogo = $empresa->getBriefingLogo();
        $form = $this->createForm(BriefingLogoType::class, $briefingLogo);

        // Procesar el formulario si se ha enviado
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (!$briefingLogo->isActivo()) {
                    throw new \Exception('El briefing logo no lo tienes activo, porfavor contácta con un administrador.');
                }

                if ($briefingLogo->getFechaCreacionBriefingLogo()) {
                    throw new \Exception('Ya has enviado un briefing de logo anteriormente.');
                }

                // Asignar datos adicionales
                $briefingLogo->setFechaCreacionBriefingLogo(new \DateTime());
                $briefingLogo->setEstado("En Progreso");
                $briefingLogo->setActivo(true);

                // Persistir el briefinglogo en la base de datos
                $em->persist($briefingLogo);
                $em->flush();

                // Mostrar un mensaje de éxito
                $this->addFlash('success', 'El Briefing del Logo se ha enviado con éxito.');

                // Redirigir a una página de éxito o realizar otras acciones necesarias
                return $this->redirectToRoute('briefing_logo_new');
            } catch (\Exception $e) {
                // Capturar la excepción y mostrar un mensaje de error al usuario
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('formularios/logo.html.twig', [
            'username' => $user->getUsername(),
            'form' => $form->createView(),
        ]);
    }
    public function show(BriefingLogo $briefingLogo): Response
    {
        $empresa = $briefingLogo->getEmpresa();

        return $this->render('dashboard/briefinglogo/show.html.twig', [
            'empresa' => $empresa,
            'briefing_logo' => $briefingLogo,
        ]);
    }



    public function delete(Request $request, BriefingLogo $briefingLogo, EntityManagerInterface $em, SessionInterface $session): Response
    {
        $empresa = $briefingLogo->getEmpresa();

        if ($this->isCsrfTokenValid('delete' . $briefingLogo->getId(), $request->request->get('_token'))) {
            $briefingLogo->setActivo(False);
            $briefingLogo->setEstado("");

            $this->addFlash('success', 'se ha eliminado éxitosamente el briefing logo de ' . $empresa->getNombre());
            $em->persist($briefingLogo);
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
