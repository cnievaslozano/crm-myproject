<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=UsuarioRepository::class)
 */
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $funcion;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $nombre_usuario;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $apellidos_usuario;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activo = true;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_creacion_usuario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Empresa", inversedBy="usuario")
     */
    private $empresa;

    public function getEmpresa(): ?Empresa
    {
        return $this->empresa;
    }

    public function setEmpresa(?Empresa $empresa): self
    {
        $this->empresa = $empresa;

        return $this;
    }

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Contacto", mappedBy="usuario")
     */
    private $contacto;
    /**
     * @return Collection|Contacto[]
     */
    public function getContacto(): Collection
    {
        return $this->contacto;
    }

    public function setContacto(?Contacto $contacto): self
    {
        $this->contacto = $contacto;

        return $this;
    }


    public function getNombreUsuario(): ?string
    {
        return $this->nombre_usuario;
    }

    public function setNombreUsuario(?string $nombre_usuario): self
    {
        $this->nombre_usuario = $nombre_usuario;

        return $this;
    }
    public function getFuncion(): ?string
    {
        return $this->funcion;
    }

    public function setFuncion(?string $funcion): self
    {
        $this->funcion = $funcion;

        return $this;
    }

    public function getApellidosUsuario(): ?string
    {
        return $this->apellidos_usuario;
    }

    public function setApellidosUsuario(?string $apellidos_usuario): self
    {
        $this->apellidos_usuario = $apellidos_usuario;

        return $this;
    }

    public function isActivo(): bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): self
    {
        $this->activo = $activo;

        return $this;
    }

    public function getFechaCreacionUsuario(): ?\DateTimeInterface
    {
        return $this->fecha_creacion_usuario;
    }

    public function setFechaCreacionUsuario(\DateTimeInterface $fecha_creacion_usuario): self
    {
        $this->fecha_creacion_usuario = $fecha_creacion_usuario;

        return $this;
    }
    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        //$roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
