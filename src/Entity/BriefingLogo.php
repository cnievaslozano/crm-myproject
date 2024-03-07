<?php

namespace App\Entity;

use App\Repository\BriefingLogoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BriefingLogoRepository::class)
 */
class BriefingLogo
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $estado;


    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $nombre_logo;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $audiencia;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $ejemplo;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $elementos;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fecha_creacion_briefing_logo;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Usuario", inversedBy="briefing_logo")
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

    public function getNombreLogo(): ?string
    {
        return $this->nombre_logo;
    }

    public function setNombreLogo(string $nombre_logo): self
    {
        $this->nombre_logo = $nombre_logo;

        return $this;
    }

    public function getAudiencia(): ?string
    {
        return $this->audiencia;
    }

    public function setAudiencia(string $audiencia): self
    {
        $this->audiencia = $audiencia;

        return $this;
    }

    public function getEjemplo(): ?string
    {
        return $this->ejemplo;
    }

    public function setEjemplo(string $ejemplo): self
    {
        $this->ejemplo = $ejemplo;

        return $this;
    }

    public function getElementos(): ?string
    {
        return $this->elementos;
    }

    public function setElementos(string $elementos): self
    {
        $this->elementos = $elementos;

        return $this;
    }

    public function getFechaCreacionBriefingLogo(): ?\DateTimeInterface
    {
        return $this->fecha_creacion_briefing_logo;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha_creacion_briefing_logo;
    }

    public function setFechaCreacionBriefingLogo(\DateTimeInterface $fecha_creacion_briefing_logo): self
    {
        $this->fecha_creacion_briefing_logo = $fecha_creacion_briefing_logo;

        return $this;
    }

    /*
        Getter para facilitar vista en twigs
    */
    public function getTipo():?string 
    {
        return "Logo";
    }
}
