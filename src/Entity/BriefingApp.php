<?php

namespace App\Entity;

use App\Repository\BriefingAppRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BriefingAppRepository::class)
 */
class BriefingApp
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activo = false;

    public function isActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): self
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $descripcion_empresa;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $descripcion_proyecto;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $objetivos;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $competencia;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $naming_slogan;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imagen_logotipo_ruta;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gama_cromatica;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $estructura_app;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_creacion_briefing_app;
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Usuario", inversedBy="briefing_app")
     * @ORM\JoinColumn(nullable=true)
     */
    private $usuario;

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

     /**
     * @ORM\OneToMany(targetEntity="App\Entity\Incidencia", mappedBy="briefing_app")
     * @ORM\JoinColumn(nullable=true)
     */
    private $incidencia;
    public function getIncidencia(): ?Incidencia
    {
        return $this->incidencia;
    }

    public function setIncidencia(?Incidencia $incidencia): self
    {
        $this->incidencia = $incidencia;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescripcionEmpresa(): ?string
    {
        return $this->descripcion_empresa;
    }

    public function setDescripcionEmpresa(string $descripcion_empresa): self
    {
        $this->descripcion_empresa = $descripcion_empresa;

        return $this;
    }

    public function getDescripcionProyecto(): ?string
    {
        return $this->descripcion_proyecto;
    }

    public function setDescripcionProyecto(?string $descripcion_proyecto): self
    {
        $this->descripcion_proyecto = $descripcion_proyecto;

        return $this;
    }

    public function getObjetivos(): ?string
    {
        return $this->objetivos;
    }

    public function setObjetivos(string $objetivos): self
    {
        $this->objetivos = $objetivos;

        return $this;
    }

    public function getCompetencia(): ?string
    {
        return $this->competencia;
    }

    public function setCompetencia(string $competencia): self
    {
        $this->competencia = $competencia;

        return $this;
    }

    public function getNamingSlogan(): ?string
    {
        return $this->naming_slogan;
    }

    public function setNamingSlogan(?string $naming_slogan): self
    {
        $this->naming_slogan = $naming_slogan;

        return $this;
    }

    public function getImagenLogotipoRuta(): ?string
    {
        return $this->imagen_logotipo_ruta;
    }

    public function setImagenLogotipoRuta(?string $imagen_logotipo_ruta): self
    {
        $this->imagen_logotipo_ruta = $imagen_logotipo_ruta;

        return $this;
    }

    public function getGamaCromatica(): ?string
    {
        return $this->gama_cromatica;
    }

    public function setGamaCromatica(?string $gama_cromatica): self
    {
        $this->gama_cromatica = $gama_cromatica;

        return $this;
    }

    public function getEstructuraApp(): ?string
    {
        return $this->estructura_app;
    }

    public function setEstructuraApp(?string $estructura_app): self
    {
        $this->estructura_app = $estructura_app;

        return $this;
    }

    public function getFechaCreacionBriefingApp(): ?\DateTimeInterface
    {
        return $this->fecha_creacion_briefing_app;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha_creacion_briefing_app;
    }

    public function setFechaCreacionBriefingApp(\DateTimeInterface $fecha_creacion_briefing_app): self
    {
        $this->fecha_creacion_briefing_app = $fecha_creacion_briefing_app;

        return $this;
    }

    /*
        Getter para facilitar vista en twigs
    */
    public function getTipo():?string 
    {
        return "App";
    }
}
