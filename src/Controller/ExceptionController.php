<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionController extends AbstractController
{
    public function showGeneralError(\Throwable $exception): Response
    {
        // Comprobamos si es un error 404
        if ($exception instanceof HttpExceptionInterface) {
            
            if ($exception->getStatusCode() == 404) {
                return $this->render('404.html.twig', [], new Response('', 404));
            }
        }

        // Si no es una excepción HTTP o es un error 404, deja que Symfony maneje el error
        throw $exception;
    }
}
