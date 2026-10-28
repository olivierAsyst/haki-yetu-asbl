<?php

namespace App\Controller\Admin;

use App\Entity\Activite;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use App\Service\SlugGenerator;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ActiviteCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly SlugGenerator $slugGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Activite::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Activité')
            ->setEntityLabelInPlural('Activités')
            ->setDefaultSort(['date' => 'DESC', 'createdAt' => 'DESC'])
            ->setPageTitle('index', "Activités de l'association")
            ->setPageTitle('new', 'Ajouter une activité')
            ->setPageTitle('edit', fn (Activite $a) => sprintf('Modifier « %s »', $a->getTitre()));
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('titre', 'Titre')
            ->setHelp('Seul champ obligatoire.');

        yield TextField::new('slug', 'Slug (URL)')
            ->setRequired(false)
            ->hideOnIndex()
            ->setHelp('Généré automatiquement à partir du titre. Modifiable si nécessaire.');

        yield TextareaField::new('description', 'Description')
            ->setRequired(false)
            ->hideOnIndex();

        yield DateField::new('date', 'Date de l\'activité')
            ->setRequired(false);

        yield TextField::new('categorie', 'Catégorie')
            ->setRequired(false)
            ->setHelp('Ex: Formation, Sensibilisation, Distribution, Événement, etc.');

        yield TextField::new('lieu', 'Lieu')
            ->setRequired(false)
            ->setHelp('Ex: Uvira, Fizi, Mwenga, Kinshasa, etc.');

        yield ImageField::new('imageName', 'Image')
            ->setBasePath('/uploads/activites')
            ->setUploadDir('public/uploads/activites')
            ->onlyOnIndex();

        yield TextField::new('imageFile', 'Photo de l\'activité')
            ->setFormType(VichImageType::class)
            ->setFormTypeOptions([
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
            ])
            ->onlyOnForms()
            ->setHelp('Formats acceptés : JPEG, PNG, WEBP (5 Mo max). Facultatif.');
    }

    public function persistEntity(\Doctrine\ORM\EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->definirSlug($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(\Doctrine\ORM\EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->definirSlug($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    /**
     * Slug saisi par l'admin, sinon tiré du titre ; toujours rendu unique.
     */
    private function definirSlug(Activite $activite): void
    {
        $source = $activite->getSlug() ?: $activite->getTitre();
        if ($source) {
            $activite->setSlug($this->slugGenerator->unique(Activite::class, $source, $activite->getId()));
        }
    }
}
