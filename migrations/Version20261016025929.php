<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261016025929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE partenaire (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, type VARCHAR(100) DEFAULT NULL, logo_name VARCHAR(255) DEFAULT NULL, logo_size INTEGER DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, actif BOOLEAN NOT NULL, ordre_affichage INTEGER DEFAULT NULL, categorie VARCHAR(100) DEFAULT NULL)');
        $this->addSql('CREATE TABLE rapport (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, annee VARCHAR(50) DEFAULT NULL, periode VARCHAR(100) DEFAULT NULL, type VARCHAR(100) DEFAULT NULL, fichier_name VARCHAR(255) DEFAULT NULL, fichier_size INTEGER DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, date_publication DATETIME DEFAULT NULL, langue VARCHAR(50) DEFAULT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE partenaire');
        $this->addSql('DROP TABLE rapport');
    }
}
