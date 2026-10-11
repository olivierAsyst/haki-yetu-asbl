<?php

namespace App\Repository;

use App\Entity\OffreEmploi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OffreEmploi>
 */
class OffreEmploiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OffreEmploi::class);
    }

    public function findActiveForHome(int $limit = 5): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.actif = true')
            ->andWhere('(o.dateExpiration IS NULL OR o.dateExpiration > :now)')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('o.datePublication', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findAllActive(): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.actif = true')
            ->andWhere('(o.dateExpiration IS NULL OR o.dateExpiration > :now)')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('o.datePublication', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
