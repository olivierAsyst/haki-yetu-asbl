<?php

namespace App\Controller\Admin;

use App\Entity\Activite;
use App\Entity\Album;
use App\Entity\Annonce;
use App\Entity\ImageAlbum;
use App\Entity\AdminUser;
use App\Entity\Message;
use App\Entity\OffreEmploi;
use App\Entity\Candidature;
use App\Entity\Partenaire;
use App\Entity\Rapport;
use App\Entity\RelationMedia;
use App\Repository\ActiviteRepository;
use App\Repository\AlbumRepository;
use App\Repository\AnnonceRepository;
use App\Repository\MessageRepository;
use App\Repository\OffreEmploiRepository;
use App\Repository\CandidatureRepository;
use App\Repository\PartenaireRepository;
use App\Repository\RapportRepository;
use App\Repository\RelationMediaRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly MessageRepository $messageRepository,
        private readonly ActiviteRepository $activiteRepository,
        private readonly AlbumRepository $albumRepository,
        private readonly AnnonceRepository $annonceRepository,
        private readonly OffreEmploiRepository $offreEmploiRepository,
        private readonly CandidatureRepository $candidatureRepository,
        private readonly PartenaireRepository $partenaireRepository,
        private readonly RapportRepository $rapportRepository,
        private readonly RelationMediaRepository $relationMediaRepository
    ) {
    }

    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'nb_messages_non_lus' => $this->messageRepository->count(['lu' => false]),
            'nb_messages_total' => $this->messageRepository->count([]),
            'nb_activites' => $this->activiteRepository->count([]),
            'nb_albums' => $this->albumRepository->count([]),
            'nb_annonces_actives' => $this->annonceRepository->count(['actif' => true]),
            'nb_annonces_total' => $this->annonceRepository->count([]),
            'nb_offres_actives' => $this->offreEmploiRepository->count(['actif' => true]),
            'nb_offres_total' => $this->offreEmploiRepository->count([]),
            'nb_candidatures' => $this->candidatureRepository->count([]),
            'latest_activites' => $this->activiteRepository->findBy([], ['createdAt' => 'DESC'], 3),
            'latest_messages' => $this->messageRepository->findBy(['lu' => false], ['createdAt' => 'DESC'], 5),
            'latest_candidatures' => $this->candidatureRepository->findBy([], ['createdAt' => 'DESC'], 5),
            'nb_partenaires_actifs' => $this->partenaireRepository->count(['actif' => true]),
            // Rapport n'a pas de champ « public » : tous les rapports sont publiés
            'nb_rapports' => $this->rapportRepository->count([]),
            'nb_medias_actifs' => $this->relationMediaRepository->count(['actif' => true]),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('HAKI YETU asbl · Back-office')
            ->setFaviconPath('favicon.ico')
            ->renderContentMaximized()
            ->generateRelativeUrls();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkToUrl('Voir le site public', 'fa fa-globe', '/')->setLinkTarget('_blank');
        yield MenuItem::section('Contenu du site');
        yield MenuItem::linkToCrud('Annonces (accueil)', 'fa fa-bullhorn', Annonce::class);
        yield MenuItem::linkToCrud('Activités', 'fa fa-hands-helping', Activite::class);
        yield MenuItem::section('Galerie');
        yield MenuItem::linkToCrud('Albums', 'fa fa-images', Album::class);
        yield MenuItem::linkToCrud('Photos', 'fa fa-image', ImageAlbum::class);
        yield MenuItem::section('Messages');
        yield MenuItem::linkToCrud('Messages reçus', 'fa fa-envelope', Message::class)
            ->setBadge($this->messageRepository->count(['lu' => false]) ?: null, 'danger');
        yield MenuItem::section('Administration');
        yield MenuItem::linkToCrud('Comptes admin', 'fa fa-user-shield', AdminUser::class);
        yield MenuItem::section('Recrutement');
        yield MenuItem::linkToCrud('Offres d\'emploi', 'fa fa-briefcase', OffreEmploi::class);
        yield MenuItem::linkToCrud('Candidatures', 'fa fa-users', Candidature::class);
        yield MenuItem::section('Relations institutionnelles');
        yield MenuItem::linkToCrud('Partenaires', 'fa fa-handshake', Partenaire::class);
        yield MenuItem::linkToCrud('Rapports', 'fa fa-file-alt', Rapport::class);
        yield MenuItem::linkToCrud('Relations médias', 'fa fa-newspaper', RelationMedia::class);
    }
}
