<?php

namespace App\Entity;

use App\Repository\ContenidoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContenidoRepository::class)
 */
class Contenido
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
    private $activo = true;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $titulo;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $punto_menu;

    /**
     * @ORM\Column(type="text")
     */
    private $contenido;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ruta_imagenes_contenidos;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_creacion_contenido;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BriefingWeb", inversedBy="contenido")
     */
    private $briefing_web;
    public function getBriefingWeb(): ?BriefingWeb
    {
        return $this->briefing_web;
    }

    public function setBriefingWeb(?BriefingWeb $briefing_web): self
    {
        $this->briefing_web = $briefing_web;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): self
    {
        $this->activo = $activo;

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

    public function getPuntoMenu(): ?string
    {
        return $this->punto_menu;
    }

    public function setPuntoMenu(string $punto_menu): self
    {
        $this->punto_menu = $punto_menu;

        return $this;
    }

    public function getContenido(): ?string
    {
        return $this->contenido;
    }

    public function setContenido(string $contenido): self
    {
        $this->contenido = $contenido;

        return $this;
    }

    public function getRutaImagenesContenidos(): ?string
    {
        return $this->ruta_imagenes_contenidos;
    }

    public function setRutaImagenesContenidos(?string $ruta_imagenes_contenidos): self
    {
        $this->ruta_imagenes_contenidos = $ruta_imagenes_contenidos;

        return $this;
    }

    public function setImagen(?string $ruta_imagenes_contenidos): self
    {
        $this->ruta_imagenes_contenidos = $ruta_imagenes_contenidos;

        return $this;
    }

    public function getFechaCreacionContenido(): ?\DateTimeInterface
    {
        return $this->fecha_creacion_contenido;
    }

    public function setFechaCreacionContenido(\DateTimeInterface $fecha_creacion_contenido): self
    {
        $this->fecha_creacion_contenido = $fecha_creacion_contenido;

        return $this;
    }
}
