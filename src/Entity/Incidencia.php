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
     * @ORM\Column(type="json", nullable=true)
     */
    private $ruta_imagenes = [];

    /**
     * @ORM\Column(type="time")
     */
    private $fecha_creacion_incidencia;

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

    public function getRutaImagenes(): ?array
    {
        return $this->ruta_imagenes;
    }

    public function setRutaImagenes(?array $ruta_imagenes): self
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
}
