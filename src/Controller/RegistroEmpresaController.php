<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Empresa;
use App\Form\EmpresaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RegistroEmpresaController extends AbstractController
{
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        // Crear una instancia del formulario
        $empresa = new Empresa();
        $form = $this->createForm(EmpresaType::class, $empresa);

        // Procesar el formulario si se ha enviado
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // datos que necesita la empresa que no se rellenan en el form
            $empresa->setFechaCreacionEmpresa(new \DateTime());
            $empresa->setActivo(true);
            $empresa->setCode();

            // Verificar si ya existe una empresa con el mismo nombre
            $existingEmpresa = $em->getRepository(Empresa::class)->findOneBy(['nombre' => $empresa->getNombre()]);

            if ($existingEmpresa) {
                // Añadir mensaje de error específico
                $this->addFlash('error', 'Ya existe una empresa con el mismo nombre.');
            } else {
                // Persistir la entidad Empresa en la base de datos
                $em->persist($empresa);
                $em->flush();
                // Añadir mensaje de éxito
                $this->addFlash('success', 'La empresa se ha registrado con éxito.');
                return $this->redirectToRoute('dashboard_clientes');
            }
        } 

        return $this->render('formularios/empresa.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
