<?php

namespace App\Repository;

use App\Entity\RelationMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RelationMedia>
 */
class RelationMediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RelationMedia::class);
    }

    public function save(RelationMedia $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RelationMedia $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Retourne tous les articles média triés par date de publication décroissante
     */
    public function findAllOrderedByPublicationDate(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.actif = :actif')
            ->setParameter('actif', true)
            ->orderBy('r.datePublication', 'DESC')
            ->addOrderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les derniers articles média (limit)
     */
    public function findLatest(int $limit = 6): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.actif = :actif')
            ->setParameter('actif', true)
            ->orderBy('r.datePublication', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les articles média par type de média
     */
    public function findByTypeMedia(?string $typeMedia): array
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.actif = :actif')
            ->setParameter('actif', true);

        if ($typeMedia) {
            $qb->andWhere('r.typeMedia = :typeMedia')
                ->setParameter('typeMedia', $typeMedia);
        }

        return $qb->orderBy('r.datePublication', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les médias distincts avec leurs logos
     */
    public function findDistinctMedia(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.nomDeMedia, r.logoMedia')
            ->distinct()
            ->andWhere('r.actif = :actif')
            ->setParameter('actif', true)
            ->andWhere('r.logoMedia IS NOT NULL')
            ->orderBy('r.nomDeMedia', 'ASC')
            ->getQuery()
            ->getResult();
    }
}