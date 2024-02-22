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


class EmpresaController extends AbstractController
{

    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        // Crear una instancia del formulario
        $empresa = new Empresa();
        $form = $this->createForm(EmpresaType::class, $empresa);

        // Procesar el formulario si se ha enviado
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //imagen
            $brochureFile = $form['imagen_logotipo_ruta']->getData();

            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('logotipos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Ha ocurrido un error al procesar la imagen.');
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $empresa->setImagenLogotipoRuta($newFilename);
            }

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
