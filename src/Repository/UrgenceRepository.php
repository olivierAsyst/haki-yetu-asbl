<?php

namespace App\Repository;

use App\Entity\Urgence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Urgence>
 */
class UrgenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Urgence::class);
    }

    /**
     * @return Urgence[] Returns an array of active Urgence objects
     */
    public function findActiveUrgences(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.estActive = :active')
            ->andWhere('u.statut != :statut')
            ->setParameter('active', true)
            ->setParameter('statut', 'urgence_terminee')
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Urgence[] Returns an array of archived Urgence objects
     */
    public function findArchivedUrgences(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.estActive = :active OR u.statut = :statut')
            ->setParameter('active', false)
            ->setParameter('statut', 'urgence_terminee')
            ->orderBy('u.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Urgence[] Returns an array of urgent Urgence objects (en_cours status)
     */
    public function findUrgentAppeals(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.statut = :statut')
            ->andWhere('u.estActive = :active')
            ->setParameter('statut', 'en_cours')
            ->setParameter('active', true)
            ->orderBy('u.dateDebut', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get total amount collected for all active emergencies
     */
    public function getTotalCollected(): float
    {
        $result = $this->createQueryBuilder('u')
            ->select('SUM(u.montantCollecte) as total')
            ->andWhere('u.estActive = :active')
            ->andWhere('u.statut != :statut')
            ->setParameter('active', true)
            ->setParameter('statut', 'urgence_terminee')
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }

    /**
     * Get total target amount for all active emergencies
     */
    public function getTotalTarget(): float
    {
        $result = $this->createQueryBuilder('u')
            ->select('SUM(u.montantCible) as total')
            ->andWhere('u.estActive = :active')
            ->andWhere('u.statut != :statut')
            ->setParameter('active', true)
            ->setParameter('statut', 'urgence_terminee')
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }

    /**
     * @return string[] Returns array of distinct emergency types
     */
    public function findDistinctTypes(): array
    {
        $results = $this->createQueryBuilder('u')
            ->select('DISTINCT u.type')
            ->orderBy('u.type', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_column($results, 'type');
    }

    /**
     * @return string[] Returns array of distinct emergency locations
     */
    public function findDistinctLocations(): array
    {
        $results = $this->createQueryBuilder('u')
            ->select('DISTINCT u.lieu')
            ->orderBy('u.lieu', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_column($results, 'lieu');
    }
}
