<?php

namespace App\Controller\Admin;

use App\Entity\Rapport;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

class RapportCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly SluggerInterface $slugger
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Rapport::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Rapport')
            ->setEntityLabelInPlural('Rapports')
            ->setDefaultSort(['annee' => 'DESC', 'createdAt' => 'DESC'])
            ->setPageTitle('index', "Gestion des rapports")
            ->setPageTitle('new', 'Ajouter un rapport')
            ->setPageTitle('edit', fn (Rapport $r) => sprintf('Modifier « %s »', $r->getTitre()))
            ->setSearchFields(['titre', 'description']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('titre', 'Titre du rapport *')
            ->setColumns(6);

        yield TextField::new('type', 'Type de rapport')
            ->setColumns(6)
            ->setHelp('Ex: Annuel, Trimestriel, Activités, Financier, etc.');

        yield TextField::new('annee', 'Année')
            ->setColumns(6)
            ->setHelp('Année du rapport (ex: 2024)');

        yield DateField::new('datePublication', 'Date de publication')
            ->setColumns(6)
            ->setRequired(false)
            ->setHelp('Date à laquelle le rapport a été publié');

        yield TextField::new('periode', 'Période ou exercice')
            ->setColumns(6)
            ->setRequired(false)
            ->hideOnIndex();

        yield ChoiceField::new('langue', 'Langue')
            ->setColumns(6)
            ->setRequired(false)
            ->setChoices([
                'Français' => 'fr',
                'Anglais' => 'en',
                'Swahili' => 'sw',
                'Lingala' => 'ln',
                'Autre' => 'autre',
            ]);

        yield TextareaField::new('description', 'Description/Résumé')
            ->setRequired(false)
            ->hideOnIndex();

        yield TextField::new('fichierName', 'Fichier')
            ->onlyOnIndex()
            ->renderAsHtml()
            ->formatValue(static function (?string $value): string {
                if (!$value) {
                    return '<span class="text-muted">Aucun fichier</span>';
                }

                return sprintf(
                    '<a href="/uploads/rapports/%s" target="_blank" rel="noopener"><i class="fas fa-file"></i> Voir</a>',
                    rawurlencode($value)
                );
            });

        yield TextField::new('fichierFile', 'Fichier du rapport')
            ->setFormType(VichFileType::class)
            ->setFormTypeOptions([
                // Obligatoire seulement à la création : pas besoin de le renvoyer en modification
                'required' => Crud::PAGE_NEW === $pageName,
                'allow_delete' => true,
                'download_uri' => false,
            ])
            ->onlyOnForms()
            ->setHelp('PDF, Word ou Excel. Taille max : 10 Mo');
    }

    public function updateEntity(\Doctrine\ORM\EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::updateEntity($entityManager, $entityInstance);
    }

    public function persistEntity(\Doctrine\ORM\EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
    }
}