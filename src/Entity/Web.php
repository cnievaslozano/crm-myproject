<?php

namespace App\Entity;

use App\Repository\WebRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WebRepository::class)
 */
class Web
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $web;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descripcion_web;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWeb(): ?string
    {
        return $this->web;
    }

    public function setWeb(string $web): self
    {
        $this->web = $web;

        return $this;
    }

    public function getDescripcionWeb(): ?string
    {
        return $this->descripcion_web;
    }

    public function setDescripcionWeb(?string $descripcion_web): self
    {
        $this->descripcion_web = $descripcion_web;

        return $this;
    }
}
