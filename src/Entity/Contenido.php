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
    private $ruta_imagenes;

    /**
     * @ORM\Column(type="time")
     */
    private $fecha_creacion_contenido;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BriefingWeb", inversedBy="contenido")
     */
    private $briefing_web;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRutaImagenes(): ?string
    {
        return $this->ruta_imagenes;
    }

    public function setRutaImagenes(?string $ruta_imagenes): self
    {
        $this->ruta_imagenes = $ruta_imagenes;

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
