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
     * @ORM\Column(type="json", nullable=true)
     */
    private $web_ejemplo = [];

    /**
     * @ORM\Column(type="text")
     */
    private $estructura_y_contenido;

    /**
     * @ORM\Column(type="text")
     */
    private $funciones;

    /**
     * @ORM\Column(type="json")
     */
    private $disenyo_imagen = [];

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
     * @ORM\Column(type="time")
     */
    private $fecha_creacion_briefing_web;

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

    public function getWebEjemplo(): ?array
    {
        return $this->web_ejemplo;
    }

    public function setWebEjemplo(?array $web_ejemplo): self
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

    public function getDisenyoImagen(): ?array
    {
        return $this->disenyo_imagen;
    }

    public function setDisenyoImagen(array $disenyo_imagen): self
    {
        $this->disenyo_imagen = $disenyo_imagen;

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

    public function setFechaCreacionBriefingWeb(\DateTimeInterface $fecha_creacion_briefing_web): self
    {
        $this->fecha_creacion_briefing_web = $fecha_creacion_briefing_web;

        return $this;
    }
}
