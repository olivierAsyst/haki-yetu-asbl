<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260922104240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activite (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, date DATETIME DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INTEGER DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE admin_user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, nom_complet VARCHAR(150) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ADMIN_EMAIL ON admin_user (email)');
        $this->addSql('CREATE TABLE album (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, date DATETIME DEFAULT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE annonce (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, contenu CLOB DEFAULT NULL, type VARCHAR(30) NOT NULL, date_evenement DATETIME DEFAULT NULL, actif BOOLEAN NOT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE image_album (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, legende VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INTEGER DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, album_id INTEGER NOT NULL, CONSTRAINT FK_2EDDEAEA1137ABCF FOREIGN KEY (album_id) REFERENCES album (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_2EDDEAEA1137ABCF ON image_album (album_id)');
        $this->addSql('CREATE TABLE message (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(150) NOT NULL, email VARCHAR(180) NOT NULL, sujet VARCHAR(255) DEFAULT NULL, message CLOB NOT NULL, lu BOOLEAN NOT NULL, created_at DATETIME NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE activite');
        $this->addSql('DROP TABLE admin_user');
        $this->addSql('DROP TABLE album');
        $this->addSql('DROP TABLE annonce');
        $this->addSql('DROP TABLE image_album');
        $this->addSql('DROP TABLE message');
    }
}
