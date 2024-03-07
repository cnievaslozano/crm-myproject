<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Empresa;
use App\Entity\BriefingWeb;
use App\Entity\BriefingLogo;
use App\Entity\BriefingApp;



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
            $web = $request->request->get('web');
            $logo = $request->request->get('logo');
            $app = $request->request->get('app');
            $idEmpresa = $request->request->get('idEmpresa');

            // Obtengo empresa
            $empresa = $em->getRepository(Empresa::class)->find($idEmpresa);

            if (!$empresa) {
                // Manejar el caso en que no se encuentre la empresa
                throw $this->createNotFoundException('No se encontró la empresa para el ID proporcionado: ' . $idEmpresa);
            }

            if ($web) {
                $briefingweb = $em->getRepository(Empresa::class)->findBriefingWebByEmpresaId($idEmpresa);
                if(is_null($briefingweb)){
                    $briefingweb = new BriefingWeb();
                    $briefingweb->setActivo(true);
                    $briefingweb->setEstado("Falta Rellenar Cliente");                    
                }

                $this->checkAndAssignBriefing($briefingweb, 'web', $empresa, $em);
            }
            if ($logo) {
                $briefinglogo = $em->getRepository(Empresa::class)->findBriefingLogoByEmpresaId($idEmpresa);
                if(is_null($briefinglogo)){
                    $briefinglogo = new BriefingLogo();
                    $briefinglogo->setActivo(true);
                    $briefinglogo->setEstado("Falta Rellenar Cliente");  
                }
                $this->checkAndAssignBriefing($briefinglogo, 'logo', $empresa, $em);
            }
            if ($app) {
                $briefingapp = $em->getRepository(Empresa::class)->findBriefingLogoByEmpresaId($idEmpresa);
                if(is_null($briefingapp)){
                    $briefingapp = new BriefingApp();
                    $briefingapp->setActivo(true);
                    $briefingapp->setEstado("Falta Rellenar Cliente");  
                }
                $this->checkAndAssignBriefing($briefingapp, 'app', $empresa, $em);
            }

            // Mensajes de éxito
            $this->addFlash('success', 'Datos recibidos correctamente.');
        } catch (\Exception $e) {
            // Mensajes de error
            $this->addFlash('error', 'Ocurrió un error al procesar los datos.'. $e);
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

    private function checkAndAssignBriefing($briefing, $type, $empresa, $em)
    {
        if ($briefing && !$briefing->isActivo()) {
            $briefing->setActivo(true);
            $em->persist($empresa);
            $em->flush();
            $this->addFlash('success', 'Se asignó correctamente el briefing ' . $type);
        } else {
            $this->addFlash('error', 'Esta empresa ya tiene un briefing ' . $type . ' asignado o no se encontró el briefing.');
        }
    }
}
