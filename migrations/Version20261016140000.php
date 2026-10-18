<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261016140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des colonnes categorie et lieu à la table activite';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activite ADD COLUMN categorie VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE activite ADD COLUMN lieu VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activite DROP COLUMN categorie');
        $this->addSql('ALTER TABLE activite DROP COLUMN lieu');
    }
}
