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

    /**
     * Totaux par devise : on n'additionne jamais des USD et des CDF entre eux.
     *
     * @return array<string, string> devise => total, ex. ['CDF' => '150000.00', 'USD' => '75.00']
     */
    public function getTotauxParDevise(?string $statut = null): array
    {
        $qb = $this->createQueryBuilder('d')
            ->select('d.devise AS devise, SUM(d.montant) AS total')
            ->groupBy('d.devise')
            ->orderBy('d.devise', 'ASC');

        if ($statut) {
            $qb->andWhere('d.statut = :statut')
                ->setParameter('statut', $statut);
        }

        $totaux = [];
        foreach ($qb->getQuery()->getArrayResult() as $ligne) {
            $totaux[$ligne['devise']] = (string) $ligne['total'];
        }

        return $totaux;
    }

    /**
     * Dons payés des derniers mois, regroupés par mois et par devise.
     * Calcul en PHP pour rester compatible SQLite et MySQL (pas de MONTH/YEAR en DQL).
     *
     * @return list<array{periode: string, devise: string, nombre: int, total: float}>
     */
    public function getStatistiquesParMois(int $months = 12): array
    {
        $dons = $this->createQueryBuilder('d')
            ->andWhere('d.createdAt >= :date')
            ->andWhere('d.statut = :statut')
            ->setParameter('date', new \DateTimeImmutable("-{$months} months"))
            ->setParameter('statut', 'paye')
            ->getQuery()
            ->getResult();

        $stats = [];
        foreach ($dons as $don) {
            $periode = $don->getCreatedAt()->format('Y-m');
            $cle = $periode.'|'.$don->getDevise();
            $stats[$cle] ??= ['periode' => $periode, 'devise' => $don->getDevise(), 'nombre' => 0, 'total' => 0.0];
            ++$stats[$cle]['nombre'];
            $stats[$cle]['total'] += (float) $don->getMontant();
        }
        krsort($stats);

        return array_values($stats);
    }

    /**
     * @return list<array{type: string, devise: string, nombre: int, total: string}>
     */
    public function getStatistiquesParTypeDon(): array
    {
        return $this->createQueryBuilder('d')
            ->select('d.typeDon AS type, d.devise AS devise, COUNT(d.id) AS nombre, SUM(d.montant) AS total')
            ->andWhere('d.statut = :statut')
            ->setParameter('statut', 'paye')
            ->groupBy('d.typeDon, d.devise')
            ->orderBy('d.devise', 'ASC')
            ->addOrderBy('total', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return list<array{methode: string, devise: string, nombre: int, total: string}>
     */
    public function getStatistiquesParMethodePaiement(): array
    {
        return $this->createQueryBuilder('d')
            ->select('d.methodePaiement AS methode, d.devise AS devise, COUNT(d.id) AS nombre, SUM(d.montant) AS total')
            ->andWhere('d.statut = :statut')
            ->setParameter('statut', 'paye')
            ->groupBy('d.methodePaiement, d.devise')
            ->orderBy('d.devise', 'ASC')
            ->addOrderBy('total', 'DESC')
            ->getQuery()
            ->getArrayResult();
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