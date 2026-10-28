<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261017090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création de la table relation_media';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE relation_media (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            titre VARCHAR(255) NOT NULL,
            nom_de_media VARCHAR(255) NOT NULL,
            date_publication DATE DEFAULT NULL,
            lien VARCHAR(500) NOT NULL,
            description CLOB DEFAULT NULL,
            logo_media VARCHAR(100) DEFAULT NULL,
            type_media VARCHAR(100) DEFAULT NULL,
            actif BOOLEAN NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        )');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE relation_media');
    }
}