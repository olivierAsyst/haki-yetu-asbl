<?php

namespace App\Controller;

use App\Entity\OffreEmploi;
use App\Repository\OffreEmploiRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

class EmploiController extends AbstractController
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    #[Route('/emplois', name: 'app_emplois')]
    public function index(
        OffreEmploiRepository $offreEmploiRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $query = $offreEmploiRepository->createQueryBuilder('o')
            ->orderBy('o.datePublication', 'DESC')
            ->getQuery();

        $offres = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('emplois/index.html.twig', [
            'offres' => $offres,
        ]);
    }

    #[Route('/emplois/{slug}', name: 'app_emplois_show')]
    public function show(
        #[MapEntity(mapping: ['slug' => 'slug'])] OffreEmploi $offre
    ): Response {
        $whatsappUrl = $this->generateWhatsAppShareLink($offre);

        return $this->render('emplois/show.html.twig', [
            'offre' => $offre,
            'whatsapp_url' => $whatsappUrl,
        ]);
    }

    private function generateWhatsAppShareLink(OffreEmploi $offre): string
    {
        $offreUrl = $this->urlGenerator->generate('app_emplois_show', ['slug' => $offre->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);

        $message = sprintf(
            "Nouvelle offre d'emploi : %s\nLieu : %s\nDate limite : %s\n\n%s",
            $offre->getTitre(),
            $offre->getLieu(),
            $offre->getFileDeadline() ? $offre->getFileDeadline()->format('d/m/Y') : 'Non spécifiée',
            $offreUrl
        );

        return 'https://wa.me/?text=' . urlencode($message);
    }
}
