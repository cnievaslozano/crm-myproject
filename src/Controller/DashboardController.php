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
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\PagerComponent\Attribute\Sortable;

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
        $ultimosBriefingsWeb = $briefingWebRepository->findLastN(3);
        $ultimosBriefingsLogo = $briefingLogoRepository->findLastN(3);
        $ultimosBriefingsApp = $briefingAppRepository->findLastN(3);

        // Obtener los últimos clientes, incidencias y contenidos
        $ultimosClientes = $empresaRepository->findLastN(3);
        $ultimasIncidencias = $incidenciaRepository->findLastN(3);
        $ultimosContenidos = $contenidoRepository->findLastN(3);

        return $this->render('dashboard/index.html.twig', [
            'ultimosbriefingsWeb' => $ultimosBriefingsWeb,
            'ultimosbriefingsLogo' => $ultimosBriefingsLogo,
            'ultimosbriefingsApp' => $ultimosBriefingsApp,
            'ultimosclientes' => $ultimosClientes,
            'ultimasincidencias' => $ultimasIncidencias,
            'ultimoscontenidos' => $ultimosContenidos,
        ]);
    }

    public function clientes(Request $request, EmpresaRepository $empresaRepository, PaginatorInterface $paginator): Response
    {
        $empresas = $empresaRepository->findAll();

        // Paginar los resultados
        $pagination = $paginator->paginate(
            $empresas,
            $request->query->getInt('page', 1),
            7
        );

        return $this->render('dashboard/clientes.html.twig', [
            'pagination' => $pagination,
        ]);
    }


    public function briefings(Request $request, BriefingAppRepository $briefingAppRepository, BriefingWebRepository $briefingWebRepository, BriefingLogoRepository $briefingLogoRepository, PaginatorInterface $paginator): Response
    {
        $briefingApps = $briefingAppRepository->findAll();
        $briefingWebs = $briefingWebRepository->findAll();
        $briefingLogos = $briefingLogoRepository->findAll();

        $briefings = array_merge($briefingApps, $briefingWebs, $briefingLogos);

        // Paginar los resultados
        $pagination = $paginator->paginate(
            $briefings, // Array de resultados a paginar
            $request->query->getInt('page', 1), // Número de página, por defecto 1
            7 // Número de elementos por página
        );

        return $this->render('dashboard/briefings.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    public function contenidos(Request $request, ContenidoRepository $contenidoRepository, PaginatorInterface $paginator): Response
    {
        $contenidos = $contenidoRepository->findAll();

        // paginar los resultados
        $pagination = $paginator->paginate(
            $contenidos, // Array de resultados a paginar
            $request->query->getInt('page', 1), // Número de página, por defecto 1
            7 // Número de elementos por página
        );

        return $this->render('dashboard/contenidos.html.twig', [
            'pagination' => $pagination,
        ]);
    }


    public function incidencias(Request $request, IncidenciaRepository $incidenciaRepository, PaginatorInterface $paginator): Response
    {

        $incidencias = $incidenciaRepository->findAll();

        // paginar los resultados
        $pagination = $paginator->paginate(
            $incidencias, // Array de resultados a paginar
            $request->query->getInt('page', 1), // Número de página, por defecto 1
            7 // Número de elementos por página
        );


        return $this->render('dashboard/incidencias.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
