<?php

namespace App\Repository;

use App\Entity\Volontaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Volontaire>
 */
class VolontaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Volontaire::class);
    }

    /**
     * @return Volontaire[] Returns an array of active Volontaire objects
     */
    public function findActiveVolunteers(): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.estActif = :actif')
            ->andWhere('v.statut IN (:statuts)')
            ->setParameter('actif', true)
            ->setParameter('statuts', ['en_attente', 'en_revue', 'accepte'])
            ->orderBy('v.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Volontaire[] Returns an array of accepted Volontaire objects
     */
    public function findAcceptedVolunteers(): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.estActif = :actif')
            ->andWhere('v.statut = :statut')
            ->setParameter('actif', true)
            ->setParameter('statut', 'accepte')
            ->orderBy('v.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Volontaire[] Returns an array of pending Volontaire objects
     */
    public function findPendingVolunteers(): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.estActif = :actif')
            ->andWhere('v.statut IN (:statuts)')
            ->setParameter('actif', true)
            ->setParameter('statuts', ['en_attente', 'en_revue'])
            ->orderBy('v.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get total count of volunteers by status
     */
    public function countByStatus(): array
    {
        $results = $this->createQueryBuilder('v')
            ->select('v.statut, COUNT(v.id) as count')
            ->andWhere('v.estActif = :actif')
            ->setParameter('actif', true)
            ->groupBy('v.statut')
            ->orderBy('v.statut', 'ASC')
            ->getQuery()
            ->getResult();

        $counts = [
            'en_attente' => 0,
            'en_revue' => 0,
            'accepte' => 0,
            'refuse' => 0,
            'archive' => 0
        ];

        foreach ($results as $result) {
            $counts[$result['statut']] = $result['count'];
        }

        return $counts;
    }

    /**
     * @return string[] Returns array of distinct volunteer locations
     */
    public function findDistinctLocations(): array
    {
        $results = $this->createQueryBuilder('v')
            ->select('DISTINCT v.lieu')
            ->orderBy('v.lieu', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_column($results, 'lieu');
    }

    /**
     * @return string[] Returns array of distinct volunteer professions
     */
    public function findDistinctProfessions(): array
    {
        $results = $this->createQueryBuilder('v')
            ->select('DISTINCT v.profession')
            ->orderBy('v.profession', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_column($results, 'profession');
    }

    /**
     * Find volunteers by skills
     */
    public function findBySkills(array $skills): array
    {
        $qb = $this->createQueryBuilder('v')
            ->andWhere('v.estActif = :actif')
            ->andWhere('v.statut = :statut')
            ->setParameter('actif', true)
            ->setParameter('statut', 'accepte');

        $orX = $qb->expr()->orX();
        foreach ($skills as $index => $skill) {
            $param = 'skill_' . $index;
            $orX->add($qb->expr()->like('v.competences', ':' . $param));
            $qb->setParameter($param, '%"' . $skill . '"%');
        }

        $qb->andWhere($orX);

        return $qb
            ->orderBy('v.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get recent volunteers (last 30 days)
     */
    public function findRecentVolunteers(int $days = 30): array
    {
        $date = new \DateTimeImmutable('-' . $days . ' days');

        return $this->createQueryBuilder('v')
            ->andWhere('v.createdAt >= :date')
            ->setParameter('date', $date)
            ->andWhere('v.estActif = :actif')
            ->setParameter('actif', true)
            ->orderBy('v.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}