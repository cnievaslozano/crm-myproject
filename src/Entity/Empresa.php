<?php

namespace App\Entity;

use App\Repository\EmpresaRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 * @ORM\Entity(repositoryClass=EmpresaRepository::class)
 */
class Empresa
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=5, max=5, exactMessage="El code de la empresa debe tener exactamente 5 letras.")
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imagen_logotipo_ruta;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descripcion_empresa;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=9)
     */
    private $telefono;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activo = true;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_creacion_empresa;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Web", mappedBy="empresa")
     */
    private $web;

     /**
     * @return Collection|Web[]
     */
    public function getWeb(): Collection
    {
        return $this->web;
    }

    public function addWeb(Web $web): self
    {
        if (!$this->web->contains($web)) {
            $this->web[] = $web;
            $web->setEmpresa($this);
        }

        return $this;
    }

    public function removeWeb(Web $web): self
    {
        if ($this->web->removeElement($web)) {
            // set the owning side to null (unless already changed)
            if ($web->getEmpresa() === $this) {
                $web->setEmpresa(null);
            }
        }

        return $this;
    }

        /**
     * @ORM\OneToOne(targetEntity="App\Entity\BriefingWeb", mappedBy="empresa", cascade={"persist", "remove"})
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
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\BriefingApp", mappedBy="empresa", cascade={"persist", "remove"})
     */
    private $briefing_app;
    public function getBriefingApp(): ?BriefingApp
    {
        return $this->briefing_app;
    }

    public function setBriefingApp(?BriefingApp $briefing_app): self
    {
        $this->briefing_app = $briefing_app;

        return $this;
    }

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\BriefingLogo", mappedBy="empresa", cascade={"persist", "remove"})
     */
    private $briefing_logo;
    public function getBriefingLogo(): ?BriefingLogo
    {
        return $this->briefing_logo;
    }

    public function setBriefingLogo(?BriefingLogo $briefing_logo): self
    {
        $this->briefing_logo = $briefing_logo;

        return $this;
    }

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Usuario", mappedBy="empresa")
     */
    private $usuario;
    
    /**
     * @return Collection|Usuario[]
     */
    public function getUsuarios(): Collection
    {
        return $this->usuario;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

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
    public function setImagen(?string $imagen_logotipo_ruta): self
    {
        $this->imagen_logotipo_ruta = $imagen_logotipo_ruta;

        return $this;
    }

    public function getDescripcionEmpresa(): ?string
    {
        return $this->descripcion_empresa;
    }

    public function setDescripcionEmpresa(?string $descripcion_empresa): self
    {
        $this->descripcion_empresa = $descripcion_empresa;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
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

    public function getFechaCreacionEmpresa(): ?\DateTimeInterface
    {
        return $this->fecha_creacion_empresa;
    }

    public function setFechaCreacionEmpresa(\DateTimeInterface $fecha_creacion_empresa): self
    {
        $this->fecha_creacion_empresa = $fecha_creacion_empresa;

        return $this;
    }
    public function setCode(): void
    {
        // Obtenemos la primera y última letra del nombre
        $firstLetter = strtoupper(substr($this->nombre, 0, 1));
        $lastLetter = strtoupper(substr($this->nombre, -1));

        // Obtenemos el mes actual
        $currentMonth = (new DateTime())->format('m');

        // Obtenemos los últimos dos dígitos del año actual
        $currentYear = (new DateTime())->format('y');

        // Combinamos las letras del nombre, el mes y los últimos dígitos del año
        $code = $firstLetter . $lastLetter . $currentMonth . $currentYear;

        // Establecemos el código generado
        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
