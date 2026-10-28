<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Crée les tables don, offre_emploi et candidature, qui n'avaient aucune migration.
 *
 * Écrite avec l'API Schema de Doctrine (et non en SQL brut) pour fonctionner
 * aussi bien sur SQLite (local) que sur MySQL (production).
 * Chaque table n'est créée que si elle n'existe pas encore : offre_emploi et
 * candidature existent déjà dans certaines bases locales.
 */
final class Version20261028090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crée les tables don, offre_emploi et candidature si elles sont absentes';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('offre_emploi')) {
            $offre = $schema->createTable('offre_emploi');
            $this->addId($offre);
            $offre->addColumn('titre', Types::STRING, ['length' => 255]);
            $offre->addColumn('description', Types::TEXT);
            $offre->addColumn('lieu', Types::STRING, ['length' => 255]);
            $offre->addColumn('type', Types::STRING, ['length' => 20]);
            $offre->addColumn('date_publication', Types::DATETIME_IMMUTABLE, ['notnull' => false]);
            $offre->addColumn('date_expiration', Types::DATETIME_IMMUTABLE, ['notnull' => false]);
            $offre->addColumn('salaire', Types::INTEGER, ['notnull' => false]);
            $offre->addColumn('salaire_info', Types::STRING, ['length' => 255, 'notnull' => false]);
            $offre->addColumn('file_deadline', Types::DATETIME_IMMUTABLE, ['notnull' => false]);
            $offre->addColumn('fichier_offre', Types::STRING, ['length' => 255, 'notnull' => false]);
            $offre->addColumn('fichier_offre_size', Types::INTEGER, ['notnull' => false]);
            $offre->addColumn('actif', Types::BOOLEAN);
            $offre->addColumn('created_at', Types::DATETIME_IMMUTABLE);
            $offre->addColumn('updated_at', Types::DATETIME_IMMUTABLE, ['notnull' => false]);
            $offre->addColumn('slug', Types::STRING, ['length' => 255, 'notnull' => false]);
        }

        if (!$schema->hasTable('candidature')) {
            $candidature = $schema->createTable('candidature');
            $this->addId($candidature);
            $candidature->addColumn('nom', Types::STRING, ['length' => 255]);
            $candidature->addColumn('prenom', Types::STRING, ['length' => 255]);
            $candidature->addColumn('email', Types::STRING, ['length' => 255]);
            $candidature->addColumn('telephone', Types::STRING, ['length' => 20]);
            $candidature->addColumn('adresse', Types::TEXT, ['notnull' => false]);
            $candidature->addColumn('cv_file', Types::STRING, ['length' => 255, 'notnull' => false]);
            $candidature->addColumn('cv_file_size', Types::INTEGER, ['notnull' => false]);
            $candidature->addColumn('lettre_motivation_file', Types::STRING, ['length' => 255, 'notnull' => false]);
            $candidature->addColumn('lettre_motivation_file_size', Types::INTEGER, ['notnull' => false]);
            $candidature->addColumn('autres_documents', Types::STRING, ['length' => 255, 'notnull' => false]);
            $candidature->addColumn('autres_documents_size', Types::INTEGER, ['notnull' => false]);
            $candidature->addColumn('message', Types::TEXT, ['notnull' => false]);
            $candidature->addColumn('created_at', Types::DATETIME_IMMUTABLE);
            $candidature->addColumn('offre_emploi_id', Types::INTEGER);
            $candidature->addIndex(['offre_emploi_id'], 'IDX_E33BD3B8B08996ED');
            $candidature->addForeignKeyConstraint('offre_emploi', ['offre_emploi_id'], ['id'], [], 'FK_E33BD3B8B08996ED');
        }

        if (!$schema->hasTable('don')) {
            $don = $schema->createTable('don');
            $this->addId($don);
            $don->addColumn('nom', Types::STRING, ['length' => 255]);
            $don->addColumn('email', Types::STRING, ['length' => 255]);
            $don->addColumn('telephone', Types::STRING, ['length' => 100, 'notnull' => false]);
            $don->addColumn('entreprise', Types::STRING, ['length' => 255, 'notnull' => false]);
            $don->addColumn('montant', Types::FLOAT);
            $don->addColumn('devise', Types::STRING, ['length' => 50]);
            $don->addColumn('type_don', Types::STRING, ['length' => 50]);
            $don->addColumn('methode_paiement', Types::STRING, ['length' => 50]);
            $don->addColumn('message', Types::TEXT, ['notnull' => false]);
            $don->addColumn('recevoir_newsletter', Types::BOOLEAN);
            $don->addColumn('anonyme', Types::BOOLEAN);
            $don->addColumn('receipt_demande', Types::BOOLEAN);
            $don->addColumn('statut', Types::STRING, ['length' => 100, 'notnull' => false]);
            $don->addColumn('reference_paiement', Types::STRING, ['length' => 255, 'notnull' => false]);
            $don->addColumn('created_at', Types::DATETIME_IMMUTABLE);
            $don->addColumn('date_paiement', Types::DATETIME_IMMUTABLE, ['notnull' => false]);
            $don->addColumn('adresse', Types::STRING, ['length' => 255, 'notnull' => false]);
            $don->addColumn('ville', Types::STRING, ['length' => 100, 'notnull' => false]);
            $don->addColumn('pays', Types::STRING, ['length' => 100, 'notnull' => false]);
            $don->addColumn('type_personne', Types::STRING, ['length' => 50, 'notnull' => false]);
        }
    }

    public function down(Schema $schema): void
    {
        // Ne supprime que la table don : offre_emploi et candidature pouvaient exister avant
        if ($schema->hasTable('don')) {
            $schema->dropTable('don');
        }
    }

    private function addId(Table $table): void
    {
        $table->addColumn('id', Types::INTEGER, ['autoincrement' => true]);
        $table->addPrimaryKeyConstraint(
            PrimaryKeyConstraint::editor()->setUnquotedColumnNames('id')->create()
        );
    }
}
