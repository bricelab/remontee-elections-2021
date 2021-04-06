<?php

namespace App\Repository;

use App\Entity\Resultat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Result;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Resultat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Resultat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Resultat[]    findAll()
 * @method Resultat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resultat::class);
    }

    public function tauxRemonteeParCommune(int $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = <<<EOT
SELECT COUNT(*) AS nb_count 
FROM resultat r, arrondissement a, commune c, departement d
WHERE r.arrondissement_id = a.id 
AND a.commune_id = c.id 
AND c.departement_id = d.id
AND d.id = :id
EOT;
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    public function nbArrondissementParDepartement(int $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = <<<EOT
SELECT COUNT(*) AS nb_count 
FROM arrondissement a, commune c, departement d
WHERE a.commune_id = c.id 
AND c.departement_id = d.id
AND d.id = :id
EOT;
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    public function totalDesVoixObtenus(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = <<<EOT
SELECT SUM(r.nb_votants) AS nb_votants, SUM(r.nb_voix_rlc) AS nb_voix_rlc,
SUM(r.nb_voix_fcbe) AS nb_voix_fcbe, SUM(r.nb_voix_duo_tt) AS nb_voix_duo_tt, SUM(a.nb_inscrits) AS nb_inscrits 
FROM resultat r, arrondissement a 
WHERE r.arrondissement_id = a.id 
EOT;
        $stmt = $conn->prepare($sql);
        $stmt->execute([]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    public function totalDesVoixObtenusParDepartement(int $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = <<<EOT
SELECT SUM(r.nb_votants) AS nb_votants, SUM(r.nb_voix_rlc) AS nb_voix_rlc, 
SUM(r.nb_voix_fcbe) AS nb_voix_fcbe, SUM(r.nb_voix_duo_tt) AS nb_voix_duo_tt, SUM(a.nb_inscrits) AS nb_inscrits 
FROM resultat r, arrondissement a, commune c, departement d 
WHERE r.arrondissement_id = a.id 
AND a.commune_id = c.id 
AND c.departement_id = d.id 
AND d.id = :id
EOT;
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }
}
