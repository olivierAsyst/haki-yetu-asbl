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



    /**
     * Recherche les annonces avec filtres
     *
     * @param string|null $type
     * @param string|null $search
     * @return Annonce[]
     */
    public function findByFilters(?string $type = null, ?string $search = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.actif = true')
            ->orderBy('a.dateEvenement', 'DESC')
            ->addOrderBy('a.createdAt', 'DESC');

        if ($type) {
            $qb->andWhere('a.type = :type')
                ->setParameter('type', $type);
        }

        if ($search) {
            $qb->andWhere('a.titre LIKE :search OR a.contenu LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les annonces récentes
     *
     * @param int $limit
     * @return Annonce[]
     */
    public function findRecent(int $limit = 6): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.actif = true')
            ->orderBy('a.dateEvenement', 'DESC')
            ->addOrderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}