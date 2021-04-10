<?php


namespace App\Dto;


/**
 * Class RemonteeFormData
 * @package App\Dto
 */
class RemonteeFormData
{
    /**
     * @var string|null
     */
    public ?string $nomMandataire = null;

    /**
     * @var string|null
     */
    public ?string $telephoneMandataire = null;

    /**
     * @var int|null
     */
    public ?int $nbVotants = 0;

    /**
     * @var int|null
     */
    public ?int $nbVoixRlc = 0;

    /**
     * @var int|null
     */
    public ?int $nbVoixFcbe = 0;

    /**
     * @var int|null
     */
    public ?int $nbVoixDuoTT = 0;

    /**
     * @var int|null
     */
    public ?int $nbNuls = 0;

    /**
     * @var string|null
     */
    public ?string $observations = null;

    /**
     * @var int|null
     */
    public ?int $arrondissement = null;
}
