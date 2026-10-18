<?php

namespace App\Controller;

use App\Repository\ActiviteRepository;
use App\Repository\AlbumRepository;
use App\Repository\AnnonceRepository;
use App\Repository\PartenaireRepository;
use App\Repository\UrgenceRepository;
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
        PartenaireRepository $partenaireRepository,
        UrgenceRepository $urgenceRepository,
    ): Response {
        return $this->render('home/index.html.twig', [
            'annonces' => $annonceRepository->findActiveForHome(),
            'activites' => $activiteRepository->findLatest(3),
            'albums' => \array_slice($albumRepository->findAllRecentFirst(), 0, 3),
            'partenaires' => $partenaireRepository->findActifs(),
            'urgences_actives' => $urgenceRepository->findActiveUrgences(),
            'urgences_urgentes' => $urgenceRepository->findUrgentAppeals(),
        ]);
    }
}
