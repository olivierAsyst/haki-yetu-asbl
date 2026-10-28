<?php

namespace App\Controller\Admin;

use App\Entity\OffreEmploi;
use App\Service\SlugGenerator;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichFileType;

class OffreEmploiCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly SlugGenerator $slugGenerator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return OffreEmploi::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Offre d\'emploi')
            ->setEntityLabelInPlural('Offres d\'emploi')
            ->setDefaultSort(['datePublication' => 'DESC'])
            ->setPageTitle('index', 'Offres d\'emploi')
            ->setPageTitle('new', 'Ajouter une offre d\'emploi')
            ->setPageTitle('edit', fn (OffreEmploi $o) => sprintf('Modifier « %s »', $o->getTitre()));
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('titre', 'Titre du poste');

        // Obligatoire : la colonne et l'entité l'exigent
        yield TextareaField::new('description', 'Description du poste')
            ->hideOnIndex();

        yield TextField::new('lieu', 'Lieu');

        yield ChoiceField::new('type', 'Type de contrat')
            ->setChoices(OffreEmploi::TYPES);

        yield DateField::new('datePublication', 'Date de publication')
            ->setFormat('dd/MM/yyyy');

        yield DateField::new('dateExpiration', 'Fin de publication')
            ->setRequired(false)
            ->setFormat('dd/MM/yyyy')
            ->setHelp('L\'offre reste visible jusqu\'à la fin de cette journée. Facultatif.');

        yield IntegerField::new('salaire', 'Salaire (en FC)')
            ->setRequired(false)
            ->hideOnIndex();

        yield TextField::new('salaireInfo', 'Informations salaire')
            ->setRequired(false)
            ->hideOnIndex();

        yield DateField::new('fileDeadline', 'Date limite de candidature')
            ->setRequired(false)
            ->setFormat('dd/MM/yyyy')
            ->setHelp('Les candidatures restent ouvertes jusqu\'à la fin de cette journée. Facultatif.');

        yield TextField::new('fichierOffre', 'Fichier de l\'offre')
            ->onlyOnIndex()
            ->renderAsHtml()
            ->formatValue(static fn (?string $fichier): string => $fichier
                ? sprintf('<a href="/uploads/offres/%s" target="_blank" rel="noopener"><i class="fas fa-file"></i> Voir</a>', rawurlencode($fichier))
                : '<span class="text-muted">—</span>');

        yield Field::new('fichierOffreFile', 'Fichier de l\'offre')
            ->setFormType(VichFileType::class)
            ->setFormTypeOptions([
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
            ])
            ->onlyOnForms()
            ->setHelp('Formats acceptés : PDF, Word (10 Mo max). Facultatif.');

        yield TextField::new('slug', 'Slug (URL)')
            ->setRequired(false)
            ->hideOnIndex()
            ->setHelp('Laissez vide pour le générer à partir du titre. S\'il existe déjà, un numéro est ajouté.');

        yield BooleanField::new('actif', 'Visible sur le site')
            ->renderAsSwitch(true);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->definirSlug($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->definirSlug($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    /**
     * Une offre qui a reçu des candidatures ne peut pas être supprimée (la base l'interdit,
     * et on perdrait les dossiers des candidats). On propose de la masquer à la place.
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $nombre = $entityInstance->getCandidatures()->count();
        if ($nombre > 0) {
            $this->addFlash('danger', sprintf(
                'L\'offre « %s » a reçu %d candidature(s) : elle ne peut pas être supprimée. Décochez « Visible sur le site » pour la masquer, ou supprimez d\'abord ses candidatures.',
                $entityInstance->getTitre(),
                $nombre
            ));

            return;
        }

        parent::deleteEntity($entityManager, $entityInstance);
    }

    /**
     * Slug saisi par l'admin (respecté), sinon tiré du titre ; toujours rendu unique.
     */
    private function definirSlug(OffreEmploi $offre): void
    {
        $source = $offre->getSlug() ?: $offre->getTitre();
        if ($source) {
            $offre->setSlug($this->slugGenerator->unique(OffreEmploi::class, $source, $offre->getId()));
        }
    }
}
