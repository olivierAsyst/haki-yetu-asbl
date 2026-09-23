<?php

namespace App\Controller;

use App\Repository\ActiviteRepository;
use App\Repository\AlbumRepository;
use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        AnnonceRepository $annonceRepository,
        ActiviteRepository $activiteRepository,
        AlbumRepository $albumRepository,
    ): Response {
        return $this->render('home/index.html.twig', [
            'annonces' => $annonceRepository->findActiveForHome(),
            'activites' => $activiteRepository->findLatest(3),
            'albums' => \array_slice($albumRepository->findAllRecentFirst(), 0, 3),
        ]);
    }
}
