<?php

namespace App\Controller;

use App\Entity\Empresa;
use App\Form\EmpresaType;
use App\Repository\EmpresaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

    public function new(Request $request, EmpresaRepository $empresaRepository): Response
    {
        $empresa = new Empresa();
        $form = $this->createForm(EmpresaType::class, $empresa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $empresaRepository->add($empresa, true);

            return $this->redirectToRoute('app_empresa_controller_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('empresa_controller_crud/new.html.twig', [
            'empresa' => $empresa,
            'form' => $form,
        ]);
    }

    public function show(Empresa $empresa): Response
    {
        return $this->render('dashboard/clientes_show.html.twig', [
            'empresa' => $empresa,
        ]);
    }

    public function edit(Request $request, Empresa $empresa, EmpresaRepository $empresaRepository): Response
    {
        $form = $this->createForm(EmpresaType::class, $empresa);
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

        return $this->renderForm('empresa_controller_crud/edit.html.twig', [
            'empresa' => $empresa,
            'form' => $form,
        ]);
    }


    public function delete(Request $request, Empresa $empresa, EmpresaRepository $empresaRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$empresa->getId(), $request->request->get('_token'))) {
            $empresaRepository->remove($empresa, true);
        }

        return $this->redirectToRoute('empresa_index', [], Response::HTTP_SEE_OTHER);
    }
}
