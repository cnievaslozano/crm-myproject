<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;


class DashboardClienteController extends AbstractController
{
    

    public function index(): Response 
    {
        $user = $this->getUser();

        $empresa = $user->getEmpresa();
        $briefingweb = $empresa->getBriefingWeb();
        $briefinglogo = $empresa->getBriefingLogo();
        $briefingapp = $empresa->getBriefingApp();


        return $this->render('dashboard_cliente/index.html.twig', [
            "empresa" => $empresa,
            "usuario" => $user,
            "briefingweb" => $briefingweb,
            "briefinglogo" => $briefinglogo,
            "briefingapp" => $briefingapp,
        ]);
    }


}
