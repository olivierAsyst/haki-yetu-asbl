<?php

namespace App\Controller\Admin;

use App\Entity\Candidature;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CandidatureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Candidature::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Candidature')
            ->setEntityLabelInPlural('Candidatures')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPageTitle('index', 'Candidatures reçues')
            ->setPageTitle('edit', 'Détails de la candidature');
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nom', 'Nom');
        yield TextField::new('prenom', 'Prénom');
        yield TextField::new('email', 'Email');
        yield TextField::new('telephone', 'Téléphone');
        yield TextField::new('adresse', 'Adresse')
            ->hideOnIndex();

        yield TextField::new('cvFile', 'CV')
            ->formatValue(function ($value, $entity) {
                if ($entity->getCvFile()) {
                    $extension = pathinfo($entity->getCvFile(), PATHINFO_EXTENSION);
                    $icon = match(strtolower($extension)) {
                        'pdf' => 'fa-file-pdf',
                        'doc', 'docx' => 'fa-file-word',
                        default => 'fa-file'
                    };
                    return sprintf(
                        '<a href="/uploads/candidatures/cv/%s" target="_blank" class="text-brand-700 hover:text-brand-800 inline-flex items-center gap-2">
                            <i class="fas %s"></i> <span>Voir le fichier</span>
                        </a>',
                        $icon,
                        $entity->getCvFile()
                    );
                }
                return '<span class="text-ink-900/50">Aucun fichier</span>';
            })
            ->onlyOnIndex();

        yield Field::new('cvFileFile', 'CV')
            ->onlyOnForms()
            ->setHelp('PDF ou Word (5 Mo max).');

        yield TextField::new('lettreMotivationFile', 'Lettre de motivation')
            ->formatValue(function ($value, $entity) {
                if ($entity->getLettreMotivationFile()) {
                    $extension = pathinfo($entity->getLettreMotivationFile(), PATHINFO_EXTENSION);
                    $icon = match(strtolower($extension)) {
                        'pdf' => 'fa-file-pdf',
                        'doc', 'docx' => 'fa-file-word',
                        default => 'fa-file'
                    };
                    return sprintf(
                        '<a href="/uploads/candidatures/lettres/%s" target="_blank" class="text-brand-700 hover:text-brand-800 inline-flex items-center gap-2">
                            <i class="fas %s"></i> <span>Voir le fichier</span>
                        </a>',
                        $icon,
                        $entity->getLettreMotivationFile()
                    );
                }
                return '<span class="text-ink-900/50">Aucun fichier</span>';
            })
            ->onlyOnIndex();

        yield Field::new('lettreMotivationFileFile', 'Lettre de motivation')
            ->onlyOnForms()
            ->setHelp('PDF ou Word (5 Mo max).');

        yield TextField::new('autresDocuments', 'Autres documents')
            ->formatValue(function ($value, $entity) {
                if ($entity->getAutresDocuments()) {
                    $extension = pathinfo($entity->getAutresDocuments(), PATHINFO_EXTENSION);
                    $icon = match(strtolower($extension)) {
                        'pdf' => 'fa-file-pdf',
                        'zip' => 'fa-file-zipper',
                        default => 'fa-file-archive'
                    };
                    return sprintf(
                        '<a href="/uploads/candidatures/autres/%s" target="_blank" class="text-brand-700 hover:text-brand-800 inline-flex items-center gap-2">
                            <i class="fas %s"></i> <span>Voir le fichier</span>
                        </a>',
                        $icon,
                        $entity->getAutresDocuments()
                    );
                }
                return '<span class="text-ink-900/50">Aucun fichier</span>';
            })
            ->onlyOnIndex();

        yield Field::new('autresDocumentsFile', 'Autres documents')
            ->onlyOnForms()
            ->setHelp('PDF ou ZIP (10 Mo max). Regroupez vos diplômes, formations, certificats, etc.');

        yield TextareaField::new('message', 'Message')
            ->hideOnIndex();

        yield AssociationField::new('offreEmploi', 'Offre postulée')
            ->hideOnForm();

        yield DateField::new('createdAt', 'Date de candidature')
            ->hideOnForm();
    }
}
