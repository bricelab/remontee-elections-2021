<?php

namespace App\Repository;

use App\Entity\Arrondissement;
use App\Entity\Commune;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Arrondissement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Arrondissement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Arrondissement[]    findAll()
 * @method Arrondissement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArrondissementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Arrondissement::class);
    }

    /**
     * @param Commune $commune
     * @return Arrondissement[] Returns an array of Arrondissement objects
     */
    public function findWithNoResultByCommune(Commune $commune): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.resultat IS NULL')
            ->andWhere('a.commune = :commune')
            ->setParameter('commune', $commune)
            ->orderBy('a.nom', 'ASC')
//            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param Commune $commune
     * @return Arrondissement[] Returns an array of Arrondissement objects
     */
    public function findWithResultByCommune(Commune $commune): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.resultat IS NOT NULL')
            ->andWhere('a.commune = :commune')
            ->setParameter('commune', $commune)
            ->orderBy('a.nom', 'ASC')
//            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }
    public function findAllIdListByCommuneId(int $id)
    {
        return $this->createQueryBuilder('a')
            ->select('a.id')
            ->addSelect('a.nom')
            ->where('a.commune = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
            ;
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

    /*
    public function findOneBySomeField($value): ?Arrondissement
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
