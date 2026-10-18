<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AboutController extends AbstractController
{
    #[Route('/a-propos', name: 'app_about')]
    public function index(): Response
    {
        return $this->render('about/index.html.twig');
    }

    #[Route('/a-propos/objectifs', name: 'app_about_objectifs')]
    public function objectifs(): Response
    {
        $objectifs = [
            [
                'titre' => 'Promotion des droits',
                'description' => 'Défendre et promouvoir les droits des personnes vivant avec handicap à travers des actions de plaidoyer et de sensibilisation.',
                'icon' => 'fas fa-balance-scale'
            ],
            [
                'titre' => 'Inclusion sociale',
                'description' => 'Favoriser l\'intégration participative des personnes handicapées dans tous les aspects de la vie sociale, économique et politique.',
                'icon' => 'fas fa-users'
            ],
            [
                'titre' => 'Autonomisation économique',
                'description' => 'Développer des programmes de formation professionnelle et d\'appui à l\'entrepreneuriat pour les personnes handicapées.',
                'icon' => 'fas fa-briefcase'
            ],
            [
                'titre' => 'Accès aux services',
                'description' => 'Améliorer l\'accès des personnes handicapées aux services de santé, d\'éducation et de protection sociale.',
                'icon' => 'fas fa-heartbeat'
            ],
            [
                'titre' => 'Renforcement des capacités',
                'description' => 'Renforcer les capacités des organisations de personnes handicapées et des acteurs institutionnels.',
                'icon' => 'fas fa-graduation-cap'
            ],
            [
                'titre' => 'Sensibilisation communautaire',
                'description' => 'Sensibiliser les communautés sur les droits et la dignité des personnes vivant avec handicap.',
                'icon' => 'fas fa-bullhorn'
            ]
        ];

        return $this->render('about/objectifs.html.twig', [
            'objectifs' => $objectifs
        ]);
    }

    #[Route('/a-propos/mission', name: 'app_about_mission')]
    public function mission(): Response
    {
        return $this->render('about/mission.html.twig');
    }

    #[Route('/a-propos/equipe', name: 'app_about_equipe')]
    public function equipe(): Response
    {
        $membres = [
            [
                'nom' => 'Président',
                'role' => 'Président du Conseil d\'Administration',
                'description' => 'Responsable de la direction générale et de la représentation de l\'association.',
                'photo' => 'membre1.jpg'
            ],
            [
                'nom' => 'Secrétaire Général',
                'role' => 'Secrétaire Exécutif',
                'description' => 'Gestion administrative et coordination des activités quotidiennes.',
                'photo' => 'membre2.jpg'
            ],
            [
                'nom' => 'Trésorier',
                'role' => 'Responsable Financier',
                'description' => 'Gestion financière et comptable de l\'association.',
                'photo' => 'membre3.jpg'
            ],
            [
                'nom' => 'Coordonnateur Programmes',
                'role' => 'Coordonnateur des Projets',
                'description' => 'Coordination des projets et programmes sur le terrain.',
                'photo' => 'membre4.jpg'
            ],
            [
                'nom' => 'Chargée de Plaidoyer',
                'role' => 'Responsable Plaidoyer',
                'description' => 'Coordination des activités de plaidoyer et de sensibilisation.',
                'photo' => 'membre5.jpg'
            ],
            [
                'nom' => 'Technicien en Réadaptation',
                'role' => 'Spécialiste Réadaptation',
                'description' => 'Appui technique aux programmes de réadaptation.',
                'photo' => 'membre6.jpg'
            ]
        ];

        return $this->render('about/equipe.html.twig', [
            'membres' => $membres
        ]);
    }
}