<?php

namespace App\Controller\Admin;

use App\Entity\OffreEmploi;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

class OffreEmploiCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly SluggerInterface $slugger
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

        yield TextareaField::new('description', 'Description du poste')
            ->setRequired(false)
            ->hideOnIndex();

        yield TextField::new('lieu', 'Lieu');

        yield ChoiceField::new('type', 'Type de contrat')
            ->setChoices(OffreEmploi::TYPES);

        yield DateField::new('datePublication', 'Date de publication')
            ->setFormat('dd/MM/yyyy HH:mm');

        yield DateField::new('dateExpiration', 'Date d\'expiration de l\'offre')
            ->setRequired(false)
            ->setFormat('dd/MM/yyyy HH:mm')
            ->setHelp('Date à laquelle l\'offre ne sera plus visible sur le site. Facultatif.');

        yield IntegerField::new('salaire', 'Salaire (en FC)')
            ->setRequired(false)
            ->hideOnIndex();

        yield TextField::new('salaireInfo', 'Informations salaire')
            ->setRequired(false)
            ->hideOnIndex();

        yield DateField::new('fileDeadline', 'Date limite de candidature')
            ->setRequired(false)
            ->setFormat('dd/MM/yyyy HH:mm')
            ->setHelp('Date limite pour que les candidats puissent postuler. Après cette date, le formulaire sera désactivé. Facultatif.');

        yield ImageField::new('fichierOffre', 'Fichier de l\'offre')
            ->setBasePath('/uploads/offres')
            ->setUploadDir('public/uploads/offres')
            ->onlyOnIndex();

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
            ->setHelp('Généré automatiquement à partir du titre. Modifiable si nécessaire.');

        yield BooleanField::new('actif', 'Visible sur le site')
            ->renderAsSwitch(true);
    }

    public function persistEntity(\Doctrine\ORM\EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance->getTitre()) {
            $slug = $this->slugger->slug($entityInstance->getTitre())->lower()->toString();
            $entityInstance->setSlug($slug);
        }
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(\Doctrine\ORM\EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance->getTitre() && (!$entityInstance->getSlug() || $entityInstance->getSlug() === '')) {
            $slug = $this->slugger->slug($entityInstance->getTitre())->lower()->toString();
            $entityInstance->setSlug($slug);
        }
        parent::updateEntity($entityManager, $entityInstance);
    }
}
