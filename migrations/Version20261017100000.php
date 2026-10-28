<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261017100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout du champ afficherDansFooter aux partenaires et renommage ordreAffichage en ordre';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partenaire ADD COLUMN afficher_dans_footer BOOLEAN DEFAULT true NOT NULL');
        
        // Renommer ordre_affichage en ordre (SQLite ne supporte pas RENAME COLUMN directement)
        $this->addSql('ALTER TABLE partenaire ADD COLUMN ordre INTEGER DEFAULT NULL');
        $this->addSql('UPDATE partenaire SET ordre = ordre_affichage');
        $this->addSql('ALTER TABLE partenaire DROP COLUMN ordre_affichage');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partenaire ADD COLUMN ordre_affichage INTEGER DEFAULT NULL');
        $this->addSql('UPDATE partenaire SET ordre_affichage = ordre');
        $this->addSql('ALTER TABLE partenaire DROP COLUMN ordre');
        $this->addSql('ALTER TABLE partenaire DROP COLUMN afficher_dans_footer');
    }
}