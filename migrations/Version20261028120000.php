<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * - Montants des dons et des urgences en DECIMAL(15,2) au lieu de float (arrondis).
 * - Lien facultatif don -> urgence (le montant collecté se met à jour avec les dons payés).
 * - Slug des offres d'emploi unique.
 *
 * Écrite avec l'API Schema de Doctrine pour fonctionner sur SQLite et sur MySQL.
 */
final class Version20261028120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Montants en DECIMAL, lien don-urgence, slug unique des offres';
    }

    public function up(Schema $schema): void
    {
        $decimal = ['type' => Type::getType(Types::DECIMAL), 'precision' => 15, 'scale' => 2];

        $don = $schema->getTable('don');
        $don->modifyColumn('montant', $decimal);
        $don->addColumn('urgence_id', Types::INTEGER, ['notnull' => false]);
        $don->addIndex(['urgence_id'], 'IDX_F8F081D9578B7FBD');
        $don->addForeignKeyConstraint('urgence', ['urgence_id'], ['id'], ['onDelete' => 'SET NULL'], 'FK_F8F081D9578B7FBD');

        $urgence = $schema->getTable('urgence');
        $urgence->modifyColumn('montant_cible', $decimal);
        $urgence->modifyColumn('montant_collecte', $decimal);

        $schema->getTable('offre_emploi')->addUniqueIndex(['slug'], 'UNIQ_132AD0D1989D9B62');
    }

    public function down(Schema $schema): void
    {
        $float = ['type' => Type::getType(Types::FLOAT)];

        $don = $schema->getTable('don');
        $don->removeForeignKey('FK_F8F081D9578B7FBD');
        $don->dropIndex('IDX_F8F081D9578B7FBD');
        $don->dropColumn('urgence_id');
        $don->modifyColumn('montant', $float);

        $urgence = $schema->getTable('urgence');
        $urgence->modifyColumn('montant_cible', $float);
        $urgence->modifyColumn('montant_collecte', $float);

        $schema->getTable('offre_emploi')->dropIndex('UNIQ_132AD0D1989D9B62');
    }
}
