<?php

namespace App\Repository;

use App\Data\OrmPaginator;
use App\Entity\Arrondissement;
use App\Entity\Resultat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Exception as DriverException;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Resultat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Resultat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Resultat[]    findAll()
 * @method Resultat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultatRepository extends ServiceEntityRepository
{
    const PAGE_SIZE = 20;

    /**
     * ResultatRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resultat::class);
    }

    /**
     * @param int $id
     * @return array
     * @throws DriverException|Exception
     */
    public function tauxRemonteeParDepartement(int $id): array
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

        return $stmt->fetchAllAssociative();
    }

    /**
     * @param int $id
     * @return array
     * @throws DriverException|Exception
     */
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

        return $stmt->fetchAllAssociative();
    }

    /**
     * @return array
     * @throws DriverException|Exception
     */
    public function totalDesVoixObtenus(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = <<<EOT
        SELECT SUM(r.nb_votants) AS nb_votants, SUM(r.nb_voix_rlc) AS nb_voix_rlc,
        SUM(r.nb_voix_fcbe) AS nb_voix_fcbe, SUM(r.nb_voix_duo_tt) AS nb_voix_duo_tt
        FROM resultat r, arrondissement a, commune c, departement d 
        WHERE r.arrondissement_id = a.id 
        AND a.commune_id = c.id 
        AND c.departement_id = d.id 
        EOT;
        $stmt = $conn->prepare($sql);
        $stmt->execute([]);

        return $stmt->fetchAllAssociative();
    }

    /**
     * @return array
     * @throws DriverException|Exception
     */
    public function totalDesInscrits(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = <<<EOT
        SELECT SUM(l.nb_inscrits) AS nb_inscrits
        FROM (SELECT DISTINCT d.nb_inscrits AS nb_inscrits 
        FROM resultat r, arrondissement a, commune c, departement d 
        WHERE r.arrondissement_id = a.id 
        AND a.commune_id = c.id 
        AND c.departement_id = d.id) l 
        EOT;
        $stmt = $conn->prepare($sql);
        $stmt->execute([]);

        return $stmt->fetchAllAssociative();
    }

    /**
     * @param int $id
     * @return array
     * @throws DriverException|Exception
     */
    public function totalDesVoixObtenusParDepartement(int $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = <<<EOT
        SELECT SUM(r.nb_votants) AS nb_votants, SUM(r.nb_voix_rlc) AS nb_voix_rlc, 
        SUM(r.nb_voix_fcbe) AS nb_voix_fcbe, SUM(r.nb_voix_duo_tt) AS nb_voix_duo_tt, SUM(d.nb_inscrits) AS nb_inscrits 
        FROM resultat r, arrondissement a, commune c, departement d 
        WHERE r.arrondissement_id = a.id 
        AND a.commune_id = c.id 
        AND c.departement_id = d.id 
        AND d.id = :id
        EOT;
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetchAllAssociative();
    }

    public function findAllPaginated(int $page = 1, int $pageSize = self::PAGE_SIZE): OrmPaginator
    {
        $query = $this->createQueryBuilder('r')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
        ;

        return (new OrmPaginator($query))->paginate($page, $pageSize);
    }

    public function searchPaginatedByArr(string $q, int $page = 1, int $pageSize = self::PAGE_SIZE): OrmPaginator
    {
        $query = $this->createQueryBuilder('r')
            ->join('r.arrondissement', 'a')
            ->andWhere('UPPER(a.nom) like :q')
            ->setParameter('q', '%'. strtoupper($q) .'%')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
        ;

        return (new OrmPaginator($query))->paginate($page, $pageSize);
    }

    public function searchPaginatedByCom(string $q, int $page = 1, int $pageSize = self::PAGE_SIZE): OrmPaginator
    {
        $query = $this->createQueryBuilder('r')
            ->join('r.arrondissement', 'a')
            ->join('a.commune', 'c')
            ->andWhere('UPPER(c.nom) like :q')
            ->setParameter('q', '%'. strtoupper($q) .'%')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
        ;

        return (new OrmPaginator($query))->paginate($page, $pageSize);
    }

    public function searchPaginatedByDep(string $q, int $page = 1, int $pageSize = self::PAGE_SIZE): OrmPaginator
    {
        $query = $this->createQueryBuilder('r')
            ->join('r.arrondissement', 'a')
            ->join('a.commune', 'c')
            ->join('c.departement', 'd')
            ->andWhere('UPPER(d.nom) like :q')
            ->setParameter('q', '%'. strtoupper($q) .'%')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
        ;

        return (new OrmPaginator($query))->paginate($page, $pageSize);
    }

    public function searchPaginatedByWarningFlag(int $page = 1, int $pageSize = self::PAGE_SIZE): OrmPaginator
    {
        $query = $this->createQueryBuilder('r')
            ->andWhere('r.warningFlag IS NOT null')
            ->andWhere('r.warningFlag = true')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
        ;

        return (new OrmPaginator($query))->paginate($page, $pageSize);
    }

    public function searchPaginatedByAll(int $page = 1, string $dep = '', string $com = '', string $arr ='', bool $flag = false, int $pageSize = self::PAGE_SIZE): OrmPaginator
    {
        $query = $this->createQueryBuilder('r')
            ->join('r.arrondissement', 'a')
            ->join('a.commune', 'c')
            ->join('c.departement', 'd')
            ->andWhere('UPPER(d.nom) like :dep')
            ->andWhere('UPPER(c.nom) like :com')
            ->andWhere('UPPER(a.nom) like :arr')
            ->setParameter('dep', '%'. strtoupper($dep) .'%')
            ->setParameter('com', '%'. strtoupper($com) .'%')
            ->setParameter('arr', '%'. strtoupper($arr) .'%')
        ;
        if ($flag) {
            $query
                ->andWhere('r.warningFlag IS NOT null')
                ->andWhere('r.warningFlag = true')
            ;
        }
        $query
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
        ;

        return (new OrmPaginator($query))->paginate($page, $pageSize);
    }
}
