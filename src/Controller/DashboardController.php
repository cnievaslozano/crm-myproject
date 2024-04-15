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
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Empresa;
use App\Service\MisFunciones;

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

    public function clientes(Request $request, MisFunciones $misFunciones, EmpresaRepository $empresaRepository, PaginatorInterface $paginator, EntityManagerInterface $em): Response
    {
        return $misFunciones->searchItems($request, $empresaRepository, $paginator, 'dashboard/clientes.html.twig');
    }

    public function briefings(Request $request, MisFunciones $misFunciones, BriefingAppRepository $briefingAppRepository, BriefingWebRepository $briefingWebRepository, BriefingLogoRepository $briefingLogoRepository, PaginatorInterface $paginator): Response
    {

        // Obtengo los filtros
        $query = $request->get('busqueda');
        $order = $request->get('order');

        $briefingApps = $briefingAppRepository->findAll();
        $briefingWebs = $briefingWebRepository->findAll();
        $briefingLogos = $briefingLogoRepository->findAll();

        $briefings = array_merge($briefingApps, $briefingWebs, $briefingLogos);
        $briefingsQuery = null;

        if ($query !== null) {

            // busqueda por código o nombre
            if (strlen($query) >= 5 && is_numeric(substr($query, -4))) {
                foreach ($briefings as $briefing) {
                    $empresa = $briefing->getEmpresa();
                    if ($empresa->getCode() == $query) {
                        $briefingsQuery[] = $briefing;
                    }
                }
            } else {
                foreach ($briefings as $briefing) {
                    $empresa = $briefing->getEmpresa();
                    if ($empresa !== null && stripos($empresa->getNombre(), $query) !== false) {
                        $briefingsQuery[] = $briefing;
                    }
                }
            }
        }

        if ($order === 'asc') {
            usort($briefings, function ($a, $b) {
                return $a->getId() - $b->getId();
            });
        } elseif ($order === 'desc') {
            usort($briefings, function ($a, $b) {
                return $b->getId() - $a->getId();
            });
        }

        // Paginar los resultados
        $pagination = $paginator->paginate(
            ($briefingsQuery) ? $briefingsQuery : $briefings,
            $request->query->getInt('page', 1),
            7
        );

        return $this->render('dashboard/briefings.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    public function contenidos(Request $request, MisFunciones $misFunciones, ContenidoRepository $contenidoRepository, PaginatorInterface $paginator): Response
    {
        return $misFunciones->searchItems($request, $contenidoRepository, $paginator, 'dashboard/contenidos.html.twig');
    }


    public function incidencias(Request $request, MisFunciones $misFunciones, IncidenciaRepository $incidenciaRepository, PaginatorInterface $paginator): Response
    {
        return $misFunciones->searchItems($request, $incidenciaRepository, $paginator, 'dashboard/incidencias.html.twig');
    }
}
