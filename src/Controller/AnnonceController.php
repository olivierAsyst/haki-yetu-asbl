<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/annonces')]
class AnnonceController extends AbstractController
{
    #[Route('/', name: 'app_annonces_index', methods: ['GET'])]
    public function index(Request $request, AnnonceRepository $annonceRepository): Response
    {
        $type = $request->query->get('type');
        $search = $request->query->get('search');
        
        $annonces = $annonceRepository->findByFilters($type, $search);

        return $this->render('annonces/index.html.twig', [
            'annonces' => $annonces,
            'types' => Annonce::TYPES,
            'currentType' => $type,
            'search' => $search,
        ]);
    }

    #[Route('/{id}', name: 'app_annonces_show', methods: ['GET'])]
    public function show(Annonce $annonce): Response
    {
        return $this->render('annonces/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }
}