<?php

namespace App\Controller\Admin;

use App\Entity\ImageAlbum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ImageAlbumCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ImageAlbum::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Photo')
            ->setEntityLabelInPlural('Photos')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPageTitle('index', 'Photos de la galerie')
            ->setPageTitle('new', 'Ajouter une photo à un album');
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('album', 'Album')
            ->setRequired(true);

        yield ImageField::new('imageName', 'Aperçu')
            ->setBasePath('/uploads/albums')
            ->setUploadDir('public/uploads/albums')
            ->onlyOnIndex();

        yield TextField::new('imageFile', 'Fichier image')
            ->setFormType(VichImageType::class)
            ->setFormTypeOptions([
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
            ])
            ->onlyOnForms()
            ->setHelp('Formats acceptés : JPEG, PNG, WEBP (5 Mo max).');

        yield TextField::new('legende', 'Légende')
            ->setRequired(false);
    }
}
