<?php

namespace App\Controller;

use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\UserType;
use App\Form\UsuarioType;

class RegistroController extends AbstractController
{
    /**
     * @Route("/registro", name="app_registro")
     */
    public function index(): Response
    {
        $user = new Usuario();
        $form = $this->createForm(UsuarioType::class, $user);
        return $this->render('registro/index.html.twig', [
            'controller_name' => 'RegistroController',
            'form' => $form->createView(),
        ]);
    }
}
