<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
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
            return $this->render('dashboard/index.html.twig');
        }

        // Si el usuario no tiene el rol de administrador, redirige a la página de login
        return $this->redirectToRoute('app_login');
    }

    public function clientes(): Response
    {
        if ($this->checkAdminRole()) {
            return $this->render('dashboard/clientes.html.twig');
        }

        // Si el usuario no tiene el rol de administrador, redirige a la página de login
        return $this->redirectToRoute('app_login');
    }


    public function briefings(): Response
    {
        if ($this->checkAdminRole()) {
            return $this->render('dashboard/briefings.html.twig');
        }

        // Si el usuario no tiene el rol de administrador, redirige a la página de login
        return $this->redirectToRoute('app_login');
    }

    public function gestorDeContenidos(): Response
    {
        if ($this->checkAdminRole()) {
            return $this->render('dashboard/gestorDeContenidos.html.twig');
        }

        // Si el usuario no tiene el rol de administrador, redirige a la página de login
        return $this->redirectToRoute('app_login');
    }

    
    public function incidencias(): Response
    {
        if ($this->checkAdminRole()) {
            return $this->render('dashboard/incidencias.html.twig');
        }

        // Si el usuario no tiene el rol de administrador, redirige a la página de login
        return $this->redirectToRoute('app_login');
    }

    public function empresa(): Response
    {

        $user = $this->getUser();
        if ($user) {
            $rol = $user->getRoles();
            if ($rol[0] == 'ROLE_USER'){
                return $this->render('dashboard/empresa.html.twig');
            } else {
                return $this->redirectToRoute('app_login');
            }
        } else {
            return $this->redirectToRoute('app_login');
        }

       
    }
}
