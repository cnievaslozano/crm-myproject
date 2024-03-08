<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Empresa;

class BriefingHandleController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function __invoke(Request $request, EntityManagerInterface $em): Response
    {
        try {
            // Obtener los datos del formulario
            $idEmpresa = $request->request->get('idEmpresa');
            $empresa = $em->getRepository(Empresa::class)->find($idEmpresa);

            if (!$empresa) {
                throw $this->createNotFoundException('No se encontró la empresa para el ID proporcionado: ' . $idEmpresa);
            }

            $this->assignBriefing($request->request->get('web'), $empresa->getBriefingWeb(), 'web', $em);
            $this->assignBriefing($request->request->get('logo'), $empresa->getBriefingLogo(), 'logo', $em);
            $this->assignBriefing($request->request->get('app'), $empresa->getBriefingApp(), 'app', $em);

        } catch (\Exception $e) {
            $this->addFlash('error', 'Ocurrió un error al procesar los datos. -> ' . $e->getMessage());
        }

        // Obtener los mensajes flash
        $successFlashMessages = $this->session->getFlashBag()->get('success');
        $errorFlashMessages = $this->session->getFlashBag()->get('error');

        // Devolver una respuesta JSON con los mensajes flash
        return new JsonResponse([
            'success' => empty($errorFlashMessages), // Si no hay mensajes de error, se considera un éxito
            'success_flash_messages' => $successFlashMessages,
            'error_flash_messages' => $errorFlashMessages
        ]);
    }

    private function assignBriefing($value, $briefing, $type, $em)
    {
        if ($value == "true" && !$briefing->isActivo()) {
            $briefing->setActivo(true);
            $briefing->setEstado("Falta Rellenar Cliente");

            $em->persist($briefing);
            $em->flush();
            $this->addFlash('success', 'Se asignó correctamente el briefing ' . $type);
        } elseif ($value == "true") {
            $this->addFlash('error', 'Ya tiene asignado un briefing ' . $type . ' esta empresa');
        }
    }
}
