<?php

namespace App\Entity;

use App\Repository\ArrondissementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ArrondissementRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Arrondissement
{
    use TimestampTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(["front_fetch"])]
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(["front_fetch"])]
    private ?string $nom;

    /**
     * @ORM\ManyToOne(targetEntity=Commune::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Commune $commune;

    /**
     * @ORM\OneToOne(targetEntity=Resultat::class, mappedBy="arrondissement", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Resultat $resultat;

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

    public function getCommune(): ?Commune
    {
        return $this->commune;
    }

    public function setCommune(?Commune $commune): self
    {
        $this->commune = $commune;

        return $this;
    }

    public function getResultat(): ?Resultat
    {
        return $this->resultat;
    }

    public function setResultat(Resultat $resultat): self
    {
        // set the owning side of the relation if necessary
        if ($resultat->getArrondissement() !== $this) {
            $resultat->setArrondissement($this);
        }

        $this->resultat = $resultat;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->nom;
    }
}
