<?php

namespace App\Repository;

use App\Entity\Partenaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Partenaire>
 */
class PartenaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Partenaire::class);
    }

    public function findActifs(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.actif = :actif')
            ->setParameter('actif', true)
            ->orderBy('p.ordreAffichage', 'ASC')
            ->addOrderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findActifsParCategorie(?string $categorie = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.actif = :actif')
            ->setParameter('actif', true)
            ->orderBy('p.ordreAffichage', 'ASC')
            ->addOrderBy('p.nom', 'ASC');

        if ($categorie) {
            $qb->andWhere('p.categorie = :categorie')
                ->setParameter('categorie', $categorie);
        }

        return $qb->getQuery()->getResult();
    }

    public function findDistinctCategories(): array
    {
        return $this->createQueryBuilder('p')
            ->select('DISTINCT p.categorie')
            ->where('p.categorie IS NOT NULL')
            ->andWhere('p.actif = :actif')
            ->setParameter('actif', true)
            ->orderBy('p.categorie', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    //    /**
    //     * @return Partenaire[] Returns an array of Partenaire objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Partenaire
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}