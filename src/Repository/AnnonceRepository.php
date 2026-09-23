<?php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Annonce>
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    /**
     * Renvoie les annonces actives à afficher sur la page d'accueil,
     * les plus proches / récentes en premier.
     *
     * @return Annonce[]
     */
    public function findActiveForHome(int $limit = 4): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.actif = true')
            ->andWhere('(a.expireAt IS NULL OR a.expireAt > :now)')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('a.dateEvenement', 'ASC')
            ->addOrderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
