<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\BriefingAppRepository;
use App\Repository\BriefingLogoRepository;
use App\Repository\BriefingWebRepository;
use App\Repository\ContenidoRepository;
use App\Repository\IncidenciaRepository;
use App\Repository\EmpresaRepository;


class DashboardController extends AbstractController
{


    public function index(
        BriefingWebRepository $briefingWebRepository,
        BriefingLogoRepository $briefingLogoRepository,
        BriefingAppRepository $briefingAppRepository,
        ContenidoRepository $contenidoRepository,
        IncidenciaRepository $incidenciaRepository,
        EmpresaRepository $empresaRepository
    ): Response {

        // Obtener los últimos 4 briefings de cada tipo
        $ultimosBriefingsWeb = $briefingWebRepository->findLastN(4);
        $ultimosBriefingsLogo = $briefingLogoRepository->findLastN(4);
        $ultimosBriefingsApp = $briefingAppRepository->findLastN(4);

        // Obtener los últimos clientes, incidencias y contenidos
        $ultimosClientes = $empresaRepository->findLastN(4);
        $ultimasIncidencias = $incidenciaRepository->findLastN(4);
        $ultimosContenidos = $contenidoRepository->findLastN(4);

        return $this->render('dashboard/index.html.twig', [
            'ultimosbriefingsWeb' => $ultimosBriefingsWeb,
            'ultimosbriefingsLogo' => $ultimosBriefingsLogo,
            'ultimosbriefingsApp' => $ultimosBriefingsApp,
            'ultimosclientes' => $ultimosClientes,
            'ultimasincidencias' => $ultimasIncidencias,
            'ultimoscontenidos' => $ultimosContenidos,
        ]);
    }

    public function clientes(EmpresaRepository $empresaRepository): Response
    {
        return $this->render('dashboard/clientes.html.twig', [
            'empresas' => $empresaRepository->findAll(),
        ]);
    }


    public function briefings(BriefingAppRepository $briefingAppRepository, BriefingWebRepository $briefingWebRepository, BriefingLogoRepository $briefingLogoRepository): Response
    {


        $briefingApps = $briefingAppRepository->findAllWithEmpresaAndUser();
        $briefingWebs = $briefingWebRepository->findAllWithEmpresaAndUser();
        $briefingLogos = $briefingLogoRepository->findAllWithEmpresaAndUser();

        $briefings = array_merge($briefingApps, $briefingWebs, $briefingLogos);

        // ordena por fecha
        usort($briefings, function ($a, $b) {
            return $a->getFecha()->getTimestamp() - $b->getFecha()->getTimestamp();
        });

        return $this->render('dashboard/briefings.html.twig', [
            'briefings' => $briefings,
        ]);
    }

    public function contenidos(ContenidoRepository $contenidoRepository): Response
    {

        $contenidos = $contenidoRepository->findAllWithBriefingWebEmpresaAndUser();

        return $this->render('dashboard/contenidos.html.twig', [
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
