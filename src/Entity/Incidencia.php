<?php

namespace App\Entity;

use App\Repository\IncidenciaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IncidenciaRepository::class)
 */
class Incidencia
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tipo;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $titulo;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $descripcion;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ruta_imagenes;

    /**
     * @ORM\Column(type="boolean")
     */
    private $solucionado;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_creacion_incidencia;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BriefingWeb", inversedBy="incidencia")
     */
    private $briefing_web;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BriefingApp", inversedBy="incidencia")
     */
    private $briefing_app;

    // Getter y Setter para BriefingWeb
    public function getBriefingWeb(): ?BriefingWeb
    {
        return $this->briefing_web;
    }

    public function setBriefingWeb(?BriefingWeb $briefing_web): self
    {
        $this->briefing_web = $briefing_web;
        return $this;
    }

    // Getter y Setter para BriefingApp
    public function getBriefingApp(): ?BriefingApp
    {
        return $this->briefing_app;
    }

    public function setBriefingApp(?BriefingApp $briefing_app): self
    {
        $this->briefing_app = $briefing_app;
        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getRutaImagenes(): ?string
    {
        return $this->ruta_imagenes;
    }

    public function setRutaImagenes(?string $ruta_imagenes): self
    {
        $this->ruta_imagenes = $ruta_imagenes;

        return $this;
    }

    public function getFechaCreacionIncidencia(): ?\DateTimeInterface
    {
        return $this->fecha_creacion_incidencia;
    }

    public function setFechaCreacionIncidencia(\DateTimeInterface $fecha_creacion_incidencia): self
    {
        $this->fecha_creacion_incidencia = $fecha_creacion_incidencia;

        return $this;
    }

    public function getSolucionado(): ?bool
    {
        return $this->solucionado;
    }

    public function setSolucionado(bool $solucionado): self
    {
        $this->solucionado = $solucionado;

        return $this;
    }
}
