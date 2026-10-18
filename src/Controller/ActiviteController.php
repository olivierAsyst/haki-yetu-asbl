<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Repository\ActiviteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

class ActiviteController extends AbstractController
{
    #[Route('/activites', name: 'app_activites')]
    public function index(
        ActiviteRepository $activiteRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        // Récupérer les filtres depuis la requête
        $filters = [
            'categorie' => $request->query->get('categorie'),
            'annee' => $request->query->get('annee'),
            'lieu' => $request->query->get('lieu'),
            'search' => $request->query->get('search'),
        ];

        // Créer la requête avec filtres
        $query = $activiteRepository->createQueryBuilder('a');

        // Appliquer les filtres
        if (!empty($filters['categorie'])) {
            $query->andWhere('a.categorie = :categorie')
                ->setParameter('categorie', $filters['categorie']);
        }

        if (!empty($filters['annee'])) {
            // Approche compatible: filtrer par plage de dates
            $startDate = new \DateTimeImmutable($filters['annee'] . '-01-01 00:00:00');
            $endDate = new \DateTimeImmutable($filters['annee'] . '-12-31 23:59:59');
            $query->andWhere('a.date >= :startDate AND a.date <= :endDate')
                ->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate);
        }

        if (!empty($filters['lieu'])) {
            $query->andWhere('a.lieu LIKE :lieu')
                ->setParameter('lieu', '%' . $filters['lieu'] . '%');
        }

        if (!empty($filters['search'])) {
            $query->andWhere('a.titre LIKE :search OR a.description LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }

        $query->orderBy('a.date', 'DESC');

        $activites = $paginator->paginate(
            $query->getQuery(),
            $request->query->getInt('page', 1),
            9
        );

        // Récupérer les valeurs distinctes pour les filtres
        $categories = $activiteRepository->findDistinctCategories();
        $annees = $activiteRepository->findDistinctAnnees();
        $lieux = $activiteRepository->findDistinctLieux();

        return $this->render('activites/index.html.twig', [
            'activites' => $activites,
            'filters' => $filters,
            'categories' => $categories,
            'annees' => $annees,
            'lieux' => $lieux,
        ]);
    }

    #[Route('/activites/{slug}', name: 'app_activites_show')]
    public function show(#[MapEntity(mapping: ['slug' => 'slug'])] Activite $activite): Response
    {
        return $this->render('activites/show.html.twig', [
            'activite' => $activite,
        ]);
    }
}