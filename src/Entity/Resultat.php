<?php

namespace App\Entity;

use App\Repository\ResultatRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ResultatRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Resultat
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
    private ?string $nomMandataire;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $telephoneMandataire;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $nbVotants;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $nbVoixRlc;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $nbVoixFcbe;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $nbVoixDuoTT;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $nbNuls;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $observations;

    /**
     * @ORM\OneToOne(targetEntity=Arrondissement::class, inversedBy="resultat", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Arrondissement $arrondissement;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $warningFlag;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomMandataire(): ?string
    {
        return $this->nomMandataire;
    }

    public function setNomMandataire(string $nomMandataire): self
    {
        $this->nomMandataire = $nomMandataire;

        return $this;
    }

    public function getTelephoneMandataire(): ?string
    {
        return $this->telephoneMandataire;
    }

    public function setTelephoneMandataire(string $telephoneMandataire): self
    {
        $this->telephoneMandataire = $telephoneMandataire;

        return $this;
    }

    public function getNbVotants(): ?int
    {
        return $this->nbVotants;
    }

    public function setNbVotants(int $nbVotants): self
    {
        $this->nbVotants = $nbVotants;

        return $this;
    }

    public function getNbVoixRlc(): ?int
    {
        return $this->nbVoixRlc;
    }

    public function setNbVoixRlc(int $nbVoixRlc): self
    {
        $this->nbVoixRlc = $nbVoixRlc;

        return $this;
    }

    public function getNbVoixFcbe(): ?int
    {
        return $this->nbVoixFcbe;
    }

    public function setNbVoixFcbe(int $nbVoixFcbe): self
    {
        $this->nbVoixFcbe = $nbVoixFcbe;

        return $this;
    }

    public function getNbVoixDuoTT(): ?int
    {
        return $this->nbVoixDuoTT;
    }

    public function setNbVoixDuoTT(int $nbVoixDuoTT): self
    {
        $this->nbVoixDuoTT = $nbVoixDuoTT;

        return $this;
    }

    public function getArrondissement(): ?Arrondissement
    {
        return $this->arrondissement;
    }

    public function setArrondissement(Arrondissement $arrondissement): self
    {
        $this->arrondissement = $arrondissement;

        return $this;
    }

    public function getNbNuls(): ?int
    {
        return $this->nbNuls;
    }

    public function setNbNuls(int $nbNuls): self
    {
        $this->nbNuls = $nbNuls;

        return $this;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations(?string $observations): self
    {
        $this->observations = $observations;

        return $this;
    }

    public function getWarningFlag(): ?bool
    {
        return $this->warningFlag;
    }

    public function setWarningFlag(?bool $warningFlag): self
    {
        $this->warningFlag = $warningFlag;

        return $this;
    }
}
