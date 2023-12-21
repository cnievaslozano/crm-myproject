<?php

namespace App\Entity;

use App\Repository\ContactoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContactoRepository::class)
 */
class Contacto
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=9, nullable=true)
     */
    private $telefono;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nombre_contacto;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descripcion_contacto;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario", inversedBy="contacto")
     */
    private $usuario;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;

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

    public function getNombreContacto(): ?string
    {
        return $this->nombre_contacto;
    }

    public function setNombreContacto(string $nombre_contacto): self
    {
        $this->nombre_contacto = $nombre_contacto;

        return $this;
    }

    public function getDescripcionContacto(): ?string
    {
        return $this->descripcion_contacto;
    }

    public function setDescripcionContacto(?string $descripcion_contacto): self
    {
        $this->descripcion_contacto = $descripcion_contacto;

        return $this;
    }
}
