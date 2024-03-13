<?php

namespace App\Controller;

use App\Entity\Empresa;
use App\Entity\BriefingApp;
use App\Entity\BriefingWeb;
use App\Entity\BriefingLogo;
use App\Form\EmpresaType;
use App\Form\EmpresaEditType;
use App\Repository\EmpresaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\MisFunciones;
use App\Repository\IncidenciaRepository;
use App\Entity\Incidencia;



class EmpresaController extends AbstractController
{
    public function new(Request $request, MisFunciones $misFunciones, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {

        // Crear una instancia del formulario
        $empresa = new Empresa();
        $briefingweb = new BriefingWeb();
        $briefingapp = new BriefingApp();
        $briefinglogo = new BriefingLogo();

        $briefingweb->setActivo(false);
        $briefingapp->setActivo(false);
        $briefinglogo->setActivo(false);

        $form = $this->createForm(EmpresaType::class, $empresa);

        // Procesar el formulario si se ha enviado
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {

                // Procesar la imagen de la empresa
                $brochureFile = $form['imagen_logotipo_ruta']->getData();

                // Validar si el archivo es una imagen
                if ($brochureFile !== null) {
                    if (!$misFunciones->validateImage($brochureFile)) {
                        $this->addFlash('error', 'El archivo no es una imagen válida');
                        return $this->redirectToRoute('empresa_new');
                    }
                    $misFunciones->processImage($empresa, "empresa_new", "logotipos_directory", $slugger, $brochureFile);
                }

                // Establecer el resto de los campos de empresa
                // datos que necesita la empresa que no se rellenan en el form
                $empresa->setFechaCreacionEmpresa(new \DateTime());
                $empresa->setActivo(true);
                $empresa->setCode();
                $briefingweb->setEmpresa($empresa);
                $briefingapp->setEmpresa($empresa);
                $briefinglogo->setEmpresa($empresa);
                $empresa->setBriefingWeb($briefingweb);
                $empresa->setBriefingApp($briefingapp);
                $empresa->setBriefingLogo($briefinglogo);


                // Verificar si ya existe una empresa con el mismo nombre
                $existingEmpresa = $em->getRepository(Empresa::class)->findOneBy(['nombre' => $empresa->getNombre()]);

                if ($existingEmpresa) {
                    // Añadir mensaje de error específico
                    $this->addFlash('error', 'Ya existe una empresa con el mismo nombre.');
                    return $this->redirectToRoute('dashboard_clientes');
                }

                // Persistir la entidad Empresa en la base de datos
                $em->persist($empresa);
                $em->flush();

                // Añadir mensaje de éxito
                $this->addFlash('success', 'La empresa se ha registrado con éxito.');
                return $this->redirectToRoute('empresa_new');
            }

            return $this->render('formularios/empresa.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $e) {
            // Manejar la excepción aquí, por ejemplo, mostrar un mensaje de error
            $this->addFlash('error', 'Ha ocurrido un error: ' . $e->getMessage());

            // Redirigir a una página de error o realizar otras acciones necesarias
            return $this->redirectToRoute('empresa_new');
        }
    }


    public function show(Empresa $empresa, EntityManagerInterface $em): Response
    {
        // Inicializar variables para almacenar los briefingwebs, briefingapps y briefinglogos
        $briefingweb = null;
        $briefingapp = null;
        $briefinglogo = null;

        $briefingweb = $empresa->getBriefingWeb();
        $briefingapp = $empresa->getBriefingApp();
        $briefinglogo = $empresa->getBriefingLogo();

        // Obtener todas las incidencias asociadas al briefing web
        $incidencias = $em->getRepository(Incidencia::class)->findByBriefingWeb($briefingweb);


        // Renderizar la vista con los datos obtenidos
        return $this->render('dashboard/empresa/show.html.twig', [
            'incidencias' => $incidencias,
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
                return $this->redirectToRoute('dashboard_clientes', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $exception) {
                $this->addFlash('error', 'Ha ocurrido un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde.');
            }
        }

        return $this->renderForm('dashboard/empresa/edit.html.twig', [
            'empresa' => $empresa,
            'form' => $form,
        ]);
    }


    public function delete(Request $request, Empresa $empresa, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $empresa->getId(), $request->request->get('_token'))) {
            $empresa->setActivo(False);

            $em->persist($empresa);
            $em->flush();
        }

        return $this->redirectToRoute('dashboard_clientes', [], Response::HTTP_SEE_OTHER);
    }
}
