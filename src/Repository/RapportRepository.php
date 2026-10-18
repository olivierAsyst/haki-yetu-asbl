<?php

namespace App\Repository;

use App\Entity\Rapport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rapport>
 */
class RapportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rapport::class);
    }

    public function findByFilters(array $filters = []): array
    {
        $qb = $this->createQueryBuilder('r')
            ->orderBy('r.datePublication', 'DESC');

        if (!empty($filters['annee'])) {
            $qb->andWhere('r.annee = :annee')
                ->setParameter('annee', $filters['annee']);
        }

        if (!empty($filters['type'])) {
            $qb->andWhere('r.type = :type')
                ->setParameter('type', $filters['type']);
        }

        if (!empty($filters['langue'])) {
            $qb->andWhere('r.langue = :langue')
                ->setParameter('langue', $filters['langue']);
        }

        if (!empty($filters['search'])) {
            $qb->andWhere('r.titre LIKE :search OR r.description LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }

        return $qb->getQuery()->getResult();
    }

    public function findDistinctAnnees(): array
    {
        return $this->createQueryBuilder('r')
            ->select('DISTINCT r.annee')
            ->where('r.annee IS NOT NULL')
            ->orderBy('r.annee', 'DESC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function findDistinctTypes(): array
    {
        return $this->createQueryBuilder('r')
            ->select('DISTINCT r.type')
            ->where('r.type IS NOT NULL')
            ->orderBy('r.type', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function findDistinctLangues(): array
    {
        return $this->createQueryBuilder('r')
            ->select('DISTINCT r.langue')
            ->where('r.langue IS NOT NULL')
            ->orderBy('r.langue', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    //    /**
    //     * @return Rapport[] Returns an array of Rapport objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Rapport
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}