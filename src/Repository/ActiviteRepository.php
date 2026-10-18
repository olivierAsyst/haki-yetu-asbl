<?php

namespace App\Repository;

use App\Entity\Activite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activite>
 */
class ActiviteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activite::class);
    }

    /**
     * @return Activite[]
     */
    public function findAllRecentFirst(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.date', 'DESC')
            ->addOrderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Activite[]
     */
    public function findLatest(int $limit = 3): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.date', 'DESC')
            ->addOrderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Activite[]
     */
    public function findByFilters(array $filters = []): array
    {
        $qb = $this->createQueryBuilder('a')
            ->orderBy('a.date', 'DESC');

        if (!empty($filters['categorie'])) {
            $qb->andWhere('a.categorie = :categorie')
                ->setParameter('categorie', $filters['categorie']);
        }

        if (!empty($filters['annee'])) {
            // Approche compatible: filtrer par plage de dates
            $startDate = new \DateTimeImmutable($filters['annee'] . '-01-01');
            $endDate = new \DateTimeImmutable($filters['annee'] . '-12-31');
            $qb->andWhere('a.date >= :startDate AND a.date <= :endDate')
                ->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate);
        }

        if (!empty($filters['lieu'])) {
            $qb->andWhere('a.lieu LIKE :lieu')
                ->setParameter('lieu', '%' . $filters['lieu'] . '%');
        }

        if (!empty($filters['search'])) {
            $qb->andWhere('a.titre LIKE :search OR a.description LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return string[]
     */
    public function findDistinctCategories(): array
    {
        return $this->createQueryBuilder('a')
            ->select('DISTINCT a.categorie')
            ->where('a.categorie IS NOT NULL')
            ->orderBy('a.categorie', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    /**
     * @return string[]
     */
    public function findDistinctAnnees(): array
    {
        // Récupérer toutes les activités avec leurs dates
        $activites = $this->createQueryBuilder('a')
            ->select('a.date')
            ->where('a.date IS NOT NULL')
            ->orderBy('a.date', 'DESC')
            ->getQuery()
            ->getResult();
        
        // Extraire les années uniques
        $annees = [];
        foreach ($activites as $activite) {
            if ($activite['date'] instanceof \DateTimeInterface) {
                $annee = $activite['date']->format('Y');
                if (!in_array($annee, $annees)) {
                    $annees[] = $annee;
                }
            }
        }
        
        // Trier par ordre décroissant
        rsort($annees);
        
        return $annees;
    }

    /**
     * @return string[]
     */
    public function findDistinctLieux(): array
    {
        return $this->createQueryBuilder('a')
            ->select('DISTINCT a.lieu')
            ->where('a.lieu IS NOT NULL')
            ->orderBy('a.lieu', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }
}