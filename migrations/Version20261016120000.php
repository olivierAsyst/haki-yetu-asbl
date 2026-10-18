<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261016120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des entités Urgence et Volontaire pour les fonctionnalités humanitaires';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE urgence (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description CLOB NOT NULL, type VARCHAR(100) NOT NULL, lieu VARCHAR(100) NOT NULL, montant_cible DOUBLE PRECISION NOT NULL, montant_collecte DOUBLE PRECISION NOT NULL, date_debut DATE NOT NULL, date_fin DATE DEFAULT NULL, est_active BOOLEAN NOT NULL, statut VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE TABLE volontaire (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, prenom VARCHAR(100) NOT NULL, nom VARCHAR(100) NOT NULL, email VARCHAR(150) NOT NULL, telephone VARCHAR(20) DEFAULT NULL, lieu VARCHAR(100) NOT NULL, profession VARCHAR(100) NOT NULL, motivation CLOB NOT NULL, competences CLOB DEFAULT NULL, domaines CLOB DEFAULT NULL, disponibilite INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, est_actif BOOLEAN NOT NULL, statut VARCHAR(20) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_8B3C89CB7B00651C ON urgence (est_active)');
        $this->addSql('CREATE INDEX IDX_8B3C89CBE7703F5E ON urgence (statut)');
        $this->addSql('CREATE INDEX IDX_8B3C89CB6F949845 ON urgence (created_at)');
        $this->addSql('CREATE INDEX IDX_ABF7B24E7B00651C ON volontaire (est_actif)');
        $this->addSql('CREATE INDEX IDX_ABF7B24EE7703F5E ON volontaire (statut)');
        $this->addSql('CREATE INDEX IDX_ABF7B24E6F949845 ON volontaire (created_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE urgence');
        $this->addSql('DROP TABLE volontaire');
    }
}