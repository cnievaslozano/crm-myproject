<?php

namespace App\Controller;

use App\Entity\Empresa;
use App\Form\EmpresaType;
use App\Form\EmpresaEditType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Repository\EmpresaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UsuarioRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\MisFunciones;


class EmpresaController extends AbstractController
{
    public function new(Request $request, MisFunciones $misFunciones, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        try {
            // Crear una instancia del formulario
            $empresa = new Empresa();
            $form = $this->createForm(EmpresaType::class, $empresa);
    
            // Procesar el formulario si se ha enviado
            $form->handleRequest($request);
    
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
