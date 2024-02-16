<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\BriefingAppRepository;
use App\Repository\BriefingLogoRepository;
use App\Repository\BriefingWebRepository;
use App\Repository\ContenidoRepository;
use App\Repository\IncidenciaRepository;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    

    public function index(): Response 
    {
        return $this->render('dashboard/index.html.twig');
    }

    public function clientes(): Response
    {
        return $this->render('dashboard/clientes.html.twig');
    }


    public function briefings(BriefingAppRepository $briefingAppRepository,BriefingWebRepository $briefingWebRepository, BriefingLogoRepository $briefingLogoRepository): Response
    {


        $briefingApps = $briefingAppRepository->findAllWithEmpresaAndUser();
        $briefingWebs = $briefingWebRepository->findAllWithEmpresaAndUser();
        $briefingLogos = $briefingLogoRepository->findAllWithEmpresaAndUser();



        return $this->render('dashboard/briefings.html.twig', [
            'briefingApps' => $briefingApps,
            'briefingWebs' => $briefingWebs,
            'briefingLogos' => $briefingLogos,
        ]);
        
    }

    public function gestorDeContenidos(ContenidoRepository $contenidoRepository): Response
    {
        
        $contenidos = $contenidoRepository->findAllWithBriefingWebEmpresaAndUser();

        return $this->render('dashboard/gestorDeContenidos.html.twig', [
            'contenidos' => $contenidos,
        ]);
    }

    
    public function incidencias(IncidenciaRepository $incidenciaRepository): Response
    {

        $incidencias = $incidenciaRepository->findAll();

        return $this->render('dashboard/incidencias.html.twig', [
            'incidencias' => $incidencias,
        ]);
    }

}
