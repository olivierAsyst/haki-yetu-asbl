<?php

namespace App\Controller;

use App\Entity\Album;
use App\Repository\AlbumRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GalerieController extends AbstractController
{
    #[Route('/galerie', name: 'app_galerie')]
    public function index(
        AlbumRepository $albumRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $query = $albumRepository->createQueryBuilder('a')
            ->orderBy('a.date', 'DESC')
            ->getQuery();

        $albums = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('galerie/index.html.twig', [
            'albums' => $albums,
        ]);
    }

    #[Route('/galerie/{id}', name: 'app_galerie_show', requirements: ['id' => '\d+'])]
    public function show(Album $album): Response
    {
        return $this->render('galerie/show.html.twig', [
            'album' => $album,
        ]);
    }
}
