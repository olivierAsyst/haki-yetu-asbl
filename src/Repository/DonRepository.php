<?php

namespace App\Repository;

use App\Entity\Don;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Don>
 */
class DonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Don::class);
    }

    /**
     * @return Don[]
     */
    public function findRecents(int $limit = 10): array
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Don[]
     */
    public function findByStatut(string $statut): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.statut = :statut')
            ->setParameter('statut', $statut)
            ->orderBy('d.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalMontant(?string $statut = null): float
    {
        $qb = $this->createQueryBuilder('d')
            ->select('SUM(d.montant) as total');

        if ($statut) {
            $qb->andWhere('d.statut = :statut')
                ->setParameter('statut', $statut);
        }

        $result = $qb->getQuery()->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }

    public function getStatistiquesParMois(int $months = 12): array
    {
        $date = new \DateTime();
        $date->modify("-{$months} months");

        return $this->createQueryBuilder('d')
            ->select('MONTH(d.createdAt) as mois, YEAR(d.createdAt) as annee, COUNT(d.id) as nombre, SUM(d.montant) as total')
            ->andWhere('d.createdAt >= :date')
            ->andWhere('d.statut = :statut')
            ->setParameter('date', $date)
            ->setParameter('statut', 'paye')
            ->groupBy('annee, mois')
            ->orderBy('annee', 'DESC')
            ->addOrderBy('mois', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getStatistiquesParTypeDon(): array
    {
        return $this->createQueryBuilder('d')
            ->select('d.typeDon as type, COUNT(d.id) as nombre, SUM(d.montant) as total')
            ->andWhere('d.statut = :statut')
            ->setParameter('statut', 'paye')
            ->groupBy('d.typeDon')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getStatistiquesParMethodePaiement(): array
    {
        return $this->createQueryBuilder('d')
            ->select('d.methodePaiement as methode, COUNT(d.id) as nombre, SUM(d.montant) as total')
            ->andWhere('d.statut = :statut')
            ->setParameter('statut', 'paye')
            ->groupBy('d.methodePaiement')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Don[] Returns an array of Don objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Don
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}