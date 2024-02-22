<?php

namespace App\Controller;

use App\Entity\Empresa;
use App\Form\EmpresaType;
use App\Form\EmpresaEditType;

use App\Repository\EmpresaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BriefingAppRepository;
use App\Repository\BriefingWebRepository;
use App\Repository\BriefingLogoRepository;
use App\Repository\UsuarioRepository;


/**
 * @Route("/empresa/controller/crud")
 */
class EmpresaController extends AbstractController
{

    public function index(EmpresaRepository $empresaRepository): Response
    {
        return $this->render('dashboard/clientes.html.twig', [
            'empresas' => $empresaRepository->findAll(),
        ]);
    }

    public function show(Empresa $empresa, UsuarioRepository $usuarioRepository): Response
    {
        // Obtener todos los usuarios asociados a la empresa
        $usuarios = $usuarioRepository->findByEmpresa($empresa);

        // Inicializar variables para almacenar los briefingwebs, briefingapps y briefinglogos
        $briefingweb = null;
        $briefingapp = null;
        $briefinglogo = null;

        // Para el primer usuario, obtener sus briefingwebs, briefingapps y briefinglogos
        $primerUsuario = $usuarios[0] ?? null;
        if ($primerUsuario !== null) {
            $briefingweb = $primerUsuario->getBriefingWeb();
            $briefingapp = $primerUsuario->getBriefingApp();
            $briefinglogo = $primerUsuario->getBriefingLogo();
        }

        // Renderizar la vista con los datos obtenidos
        return $this->render('dashboard/empresa/show.html.twig', [
            'empresa' => $empresa,
            'briefingweb' => $briefingweb,
            'briefingapp' => $briefingapp,
            'briefinglogo' => $briefinglogo,
        ]);
    }




    public function edit(Request $request, Empresa $empresa, EmpresaRepository $empresaRepository): Response
    {
        $form = $this->createForm(EmpresaEditType::class, $empresa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $empresaRepository->add($empresa, true);
                $this->addFlash('success', 'Se ha actualizado la empresa con éxito.');
                return $this->redirectToRoute('empresa_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $exception) {
                $this->addFlash('error', 'Ha ocurrido un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde.');
            }
        }

        return $this->renderForm('dashboard/empresa/edit.html.twig', [
            'empresa' => $empresa,
            'form' => $form,
        ]);
    }


    public function delete(Request $request, Empresa $empresa, EmpresaRepository $empresaRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $empresa->getId(), $request->request->get('_token'))) {
            $empresaRepository->remove($empresa, true);
        }

        return $this->redirectToRoute('empresa_index', [], Response::HTTP_SEE_OTHER);
    }
}
