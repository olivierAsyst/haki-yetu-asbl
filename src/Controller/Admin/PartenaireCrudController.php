<?php

namespace App\Controller\Admin;

use App\Entity\Partenaire;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\String\Slugger\SluggerInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PartenaireCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly SluggerInterface $slugger
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Partenaire::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Partenaire')
            ->setEntityLabelInPlural('Partenaires')
            ->setDefaultSort(['ordre' => 'ASC', 'nom' => 'ASC'])
            ->setPageTitle('index', "Gestion des partenaires")
            ->setPageTitle('new', 'Ajouter un partenaire')
            ->setPageTitle('edit', fn (Partenaire $p) => sprintf('Modifier « %s »', $p->getNom()))
            ->setSearchFields(['nom', 'description', 'type']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nom', 'Nom du partenaire *')
            ->setColumns(6);

        yield TextField::new('type', 'Type de partenaire')
            ->setColumns(6)
            ->setHelp('Ex: Institutionnel, Financier, Technique, Logistique, etc.');

        yield TextareaField::new('description', 'Description')
            ->setRequired(false)
            ->hideOnIndex();

        yield UrlField::new('url', 'Site web')
            ->setRequired(false)
            ->setHelp('URL vers le site du partenaire (facultatif)')
            ->setColumns(6);

        yield IntegerField::new('ordre', 'Ordre d\'affichage')
            ->setColumns(6)
            ->setHelp('Nombre pour trier l\'affichage (plus petit = plus haut)');

        yield BooleanField::new('actif', 'Actif')
            ->setColumns(6)
            ->renderAsSwitch(true);

        yield BooleanField::new('afficherDansFooter', 'Afficher dans footer')
            ->setColumns(6)
            ->renderAsSwitch(true)
            ->setHelp('Le logo apparaîtra dans le carrousel du footer');

        yield ImageField::new('logoName', 'Logo')
            ->setBasePath('/uploads/partenaires')
            ->setUploadDir('public/uploads/partenaires')
            ->onlyOnIndex();

        yield TextField::new('logoFile', 'Logo du partenaire *')
            ->setFormType(VichImageType::class)
            ->setFormTypeOptions([
                'required' => Crud::PAGE_NEW === $pageName,
                'allow_delete' => true,
                'download_uri' => false,
            ])
            ->onlyOnForms()
            ->setHelp('Format recommandé : PNG avec fond transparent. Taille max : 2 Mo (JPEG, PNG ou WEBP)');
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