<?php

namespace App\Controller;

use App\Entity\Empresa;
use App\Form\EmpresaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistroEmpresaController extends AbstractController
{
    private function checkAdminRole(): bool
    {
        $user = $this->getUser();

        if (!$user) {
            // Si no hay usuario autenticado, redirige a la página de login
            $this->redirectToRoute('app_login');
            return false;
        } else {
            $roles = $user->getRoles();
        }
        

        return in_array('ROLE_ADMIN', $roles, true);
    }
    public function index(): Response
    {
        if ($this->checkAdminRole()) {
            $empresa = new Empresa();
            $form = $this->createForm(EmpresaType::class, $empresa); 
            return $this->render('registro_empresa/index.html.twig', [
                'controller_name' => 'RegistroEmpresaController',
                'form' => $form->createView(),
            ]);
        } else {
            // Si el usuario no tiene el rol de administrador, redirige a la página de login
            return $this->redirectToRoute('app_login');
        }
    }
}