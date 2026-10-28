<?php

namespace App\DataFixtures;

use App\Entity\RelationMedia;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RelationMediaFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Données de démonstration pour les relations médias
        $medias = [
            [
                'titre' => 'HAKI YETU asbl plaide pour l\'inclusion des personnes handicapées à Uvira',
                'nomDeMedia' => 'Radio Maendeleo',
                'datePublication' => new \DateTime('2026-06-15'),
                'lien' => 'https://radiomaendeleo.cd/article-haki-yetu-uvira',
                'description' => 'Interview avec le coordinateur de HAKI YETU asbl sur les défis de l\'inclusion des personnes handicapées dans la région d\'Uvira.',
                'logoMedia' => 'https://radiomaendeleo.cd/logo.png',
                'typeMedia' => 'Radio',
                'actif' => true
            ],
            [
                'titre' => 'Une association locale milite pour l\'accès à l\'éducation des enfants handicapés',
                'nomDeMedia' => 'CongoWeb',
                'datePublication' => new \DateTime('2026-05-22'),
                'lien' => 'https://congoweb.news/article-education-handicap',
                'description' => 'Reportage sur le programme d\'éducation inclusive de HAKI YETU asbl dans les écoles de Fizi et Mwenga.',
                'logoMedia' => 'https://congoweb.news/logo.png',
                'typeMedia' => 'Site Web',
                'actif' => true
            ],
            [
                'titre' => 'Distribution de kits d\'hygiène aux personnes handicapées lors de la crise de choléra',
                'nomDeMedia' => 'RFI Afrique',
                'datePublication' => new \DateTime('2026-04-10'),
                'lien' => 'https://www.rfi.fr/afrique/article-cholera-rdc-handicap',
                'description' => 'Reportage sur l\'action humanitaire de HAKI YETU asbl pendant l\'épidémie de choléra à Uvira.',
                'logoMedia' => 'https://www.rfi.fr/logo.png',
                'typeMedia' => 'Radio internationale',
                'actif' => true
            ],
            [
                'titre' => 'Formation en métiers pour les jeunes handicapés au Sud-Kivu',
                'nomDeMedia' => 'Journal Le Potentiel',
                'datePublication' => new \DateTime('2026-03-18'),
                'lien' => 'https://lepotentiel.cd/article-formation-handicap-sud-kivu',
                'description' => 'Article sur le programme de formation professionnelle de HAKI YETU asbl pour les jeunes handicapés.',
                'logoMedia' => 'https://lepotentiel.cd/logo.png',
                'typeMedia' => 'Journal',
                'actif' => true
            ],
            [
                'titre' => 'Une voix pour les droits des personnes handicapées en RD Congo',
                'nomDeMedia' => 'ONU Info',
                'datePublication' => new \DateTime('2026-02-05'),
                'lien' => 'https://news.un.org/fr/article-droits-handicap-rdc',
                'description' => 'Reportage sur le plaidoyer de HAKI YETU asbl pour les droits des personnes handicapées auprès des autorités congolaises.',
                'logoMedia' => 'https://news.un.org/logo.png',
                'typeMedia' => 'Site web international',
                'actif' => true
            ],
            [
                'titre' => 'Tournoi sportif inclusif à Uvira : handicapés et valides jouent ensemble',
                'nomDeMedia' => 'Radio Uvira',
                'datePublication' => new \DateTime('2026-01-20'),
                'lien' => 'https://radiouvira.cd/article-sport-inclusif',
                'description' => 'Reportage sur le tournoi sportif inclusif organisé par HAKI YETU asbl pour promouvoir l\'inclusion par le sport.',
                'logoMedia' => 'https://radiouvira.cd/logo.png',
                'typeMedia' => 'Radio',
                'actif' => true
            ]
        ];

        foreach ($medias as $mediaData) {
            $relationMedia = new RelationMedia();
            $relationMedia->setTitre($mediaData['titre']);
            $relationMedia->setNomDeMedia($mediaData['nomDeMedia']);
            $relationMedia->setDatePublication($mediaData['datePublication']);
            $relationMedia->setLien($mediaData['lien']);
            $relationMedia->setDescription($mediaData['description']);
            $relationMedia->setLogoMedia($mediaData['logoMedia']);
            $relationMedia->setTypeMedia($mediaData['typeMedia']);
            $relationMedia->setActif($mediaData['actif']);
            
            $manager->persist($relationMedia);
        }

        $manager->flush();
    }
}