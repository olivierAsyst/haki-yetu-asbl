<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Génère un slug d'URL unique pour une entité : « mon-titre », puis « mon-titre-2 », « mon-titre-3 »...
 * Évite l'erreur 500 (contrainte d'unicité) quand deux éléments ont le même titre.
 */
class SlugGenerator
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param class-string $classe   entité concernée (doit avoir les propriétés id et slug)
     * @param string       $texte    slug saisi par l'admin, ou à défaut le titre
     * @param int|null     $idActuel id de l'élément modifié, pour ne pas se compter soi-même
     */
    public function unique(string $classe, string $texte, ?int $idActuel = null): string
    {
        $base = $this->slugger->slug($texte)->lower()->toString() ?: 'element';
        $slug = $base;

        for ($i = 2; $this->existe($classe, $slug, $idActuel); ++$i) {
            $slug = $base.'-'.$i;
        }

        return $slug;
    }

    private function existe(string $classe, string $slug, ?int $idActuel): bool
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(e.id)')
            ->from($classe, 'e')
            ->where('e.slug = :slug')
            ->setParameter('slug', $slug);

        if (null !== $idActuel) {
            $qb->andWhere('e.id != :id')->setParameter('id', $idActuel);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
