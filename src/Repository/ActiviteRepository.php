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
}
