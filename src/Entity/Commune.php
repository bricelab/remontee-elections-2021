<?php

namespace App\Entity;

use App\Repository\CommuneRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommuneRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Commune
{
    use TimestampTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $nom;

    /**
     * @ORM\ManyToOne(targetEntity=Departement::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Departement $departement;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $ce;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->nom;
    }

    public function getCe(): ?int
    {
        return $this->ce;
    }

    public function setCe(?int $ce): self
    {
        $this->ce = $ce;

        return $this;
    }
}
