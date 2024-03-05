<?php

namespace App\Entity;

use App\Repository\BriefingWebRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BriefingWebRepository::class)
 */
class BriefingWeb
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $estado;

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
     * @ORM\Column(type="string", length=255)
     */
    private $productos_y_o_servicios;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $competencia;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $objetivos;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $web_ejemplo;

    /**
     * @ORM\Column(type="text")
     */
    private $estructura_y_contenido;

    /**
     * @ORM\Column(type="text")
     */
    private $funciones;

    /**
     * @ORM\Column(type="boolean")
     */
    private $disponeLogo;

    /**
     * @ORM\Column(type="boolean")
     */
    private $disponeManualMarca;

    /**
     * @ORM\Column(type="boolean")
     */
    private $disponeColoresCorporativos;

    /**
     * @ORM\Column(type="boolean")
     */
    private $disponeRecursosDisenyo;

    /**
     * @ORM\Column(type="boolean")
     */
    private $mantenimiento;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dominio;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $comentarios;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_creacion_briefing_web;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Usuario", inversedBy="briefing_web")
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
     * @ORM\OneToMany(targetEntity="App\Entity\Contenido", mappedBy="briefing_web")
     * @ORM\JoinColumn(nullable=true)
     */
    private $contenido;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Incidencia", mappedBy="briefing_web")
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
    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(?string $estado): self
    {
        $this->estado = $estado;

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

    public function getProductosYOServicios(): ?string
    {
        return $this->productos_y_o_servicios;
    }

    public function setProductosYOServicios(string $productos_y_o_servicios): self
    {
        $this->productos_y_o_servicios = $productos_y_o_servicios;

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

    public function getObjetivos(): ?string
    {
        return $this->objetivos;
    }

    public function setObjetivos(string $objetivos): self
    {
        $this->objetivos = $objetivos;

        return $this;
    }

    public function getWebEjemplo(): ?string
    {
        return $this->web_ejemplo;
    }

    public function setWebEjemplo(?string $web_ejemplo): self
    {
        $this->web_ejemplo = $web_ejemplo;

        return $this;
    }

    public function getEstructuraYContenido(): ?string
    {
        return $this->estructura_y_contenido;
    }

    public function setEstructuraYContenido(string $estructura_y_contenido): self
    {
        $this->estructura_y_contenido = $estructura_y_contenido;

        return $this;
    }

    public function getFunciones(): ?string
    {
        return $this->funciones;
    }

    public function setFunciones(string $funciones): self
    {
        $this->funciones = $funciones;

        return $this;
    }

    public function isMantenimiento(): ?bool
    {
        return $this->mantenimiento;
    }

    public function setMantenimiento(bool $mantenimiento): self
    {
        $this->mantenimiento = $mantenimiento;

        return $this;
    }

    public function getDominio(): ?string
    {
        return $this->dominio;
    }

    public function setDominio(string $dominio): self
    {
        $this->dominio = $dominio;

        return $this;
    }

    public function getComentarios(): ?string
    {
        return $this->comentarios;
    }

    public function setComentarios(?string $comentarios): self
    {
        $this->comentarios = $comentarios;

        return $this;
    }

    public function getFechaCreacionBriefingWeb(): ?\DateTimeInterface
    {
        return $this->fecha_creacion_briefing_web;
    }
    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha_creacion_briefing_web;
    }

    public function setFechaCreacionBriefingWeb(\DateTimeInterface $fecha_creacion_briefing_web): self
    {
        $this->fecha_creacion_briefing_web = $fecha_creacion_briefing_web;

        return $this;
    }

    // Getters y setters para disponeLogo
    public function getDisponeLogo(): ?bool
    {
        return $this->disponeLogo;
    }

    public function setDisponeLogo(bool $disponeLogo): self
    {
        $this->disponeLogo = $disponeLogo;

        return $this;
    }

    // Getters y setters para disponeManualMarca
    public function getDisponeManualMarca(): ?bool
    {
        return $this->disponeManualMarca;
    }

    public function setDisponeManualMarca(bool $disponeManualMarca): self
    {
        $this->disponeManualMarca = $disponeManualMarca;

        return $this;
    }

    // Getters y setters para disponeColoresCorporativos
    public function getDisponeColoresCorporativos(): ?bool
    {
        return $this->disponeColoresCorporativos;
    }

    public function setDisponeColoresCorporativos(bool $disponeColoresCorporativos): self
    {
        $this->disponeColoresCorporativos = $disponeColoresCorporativos;

        return $this;
    }

    // Getters y setters para disponeRecursosDisenyo
    public function getDisponeRecursosDisenyo(): ?bool
    {
        return $this->disponeRecursosDisenyo;
    }

    public function setDisponeRecursosDisenyo(bool $disponeRecursosDisenyo): self
    {
        $this->disponeRecursosDisenyo = $disponeRecursosDisenyo;

        return $this;
    }

    /*
        Getter para facilitar vista en twigs
    */
    public function getTipo(): ?string
    {
        return "Web";
    }
}
