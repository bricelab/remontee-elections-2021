<?php


namespace App\Dto;


use App\Entity\Arrondissement;

class RemonteeFormData
{
    public ?string $nomMandataire = null;

    public ?string $telephoneMandataire = null;

    public ?int $nbVotants = 0;

    public ?int $nbVoixRlc = 0;

    public ?int $nbVoixFcbe = 0;

    public ?int $nbVoixDuoTT = 0;

    public ?int $nbNuls = 0;

    public ?string $observations = null;

    public ?int $arrondissement = null;
}
