<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;


class DashboardClienteController extends AbstractController
{
    

    public function index(): Response 
    {
        return $this->render('dashboard_cliente/index.html.twig');
    }


}
