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
        $query = $activiteRepository->createQueryBuilder('a')
            ->orderBy('a.date', 'DESC')
            ->getQuery();

        $activites = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('activites/index.html.twig', [
            'activites' => $activites,
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
