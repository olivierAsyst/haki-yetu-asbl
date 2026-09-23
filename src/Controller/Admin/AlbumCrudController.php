<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AlbumCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Album::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Album')
            ->setEntityLabelInPlural('Albums')
            ->setDefaultSort(['date' => 'DESC', 'createdAt' => 'DESC'])
            ->setPageTitle('index', 'Albums de la galerie')
            ->setPageTitle('new', 'Créer un album')
            ->setHelp('index', "Créez d'abord l'album, puis ajoutez ses photos depuis le menu « Photos ».");
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('titre', 'Titre')
            ->setHelp('Seul champ obligatoire.');

        yield TextareaField::new('description', 'Description')
            ->setRequired(false)
            ->hideOnIndex();

        yield DateField::new('date', "Date de l'album")
            ->setRequired(false);

        yield AssociationField::new('images', 'Photos')
            ->onlyOnIndex()
            ->formatValue(fn ($value, Album $album) => \count($album->getImages()).' photo(s)');
    }
}
