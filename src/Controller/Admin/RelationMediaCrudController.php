<?php

namespace App\Controller\Admin;

use App\Entity\RelationMedia;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\String\Slugger\SluggerInterface;

class RelationMediaCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly SluggerInterface $slugger
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return RelationMedia::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Article média')
            ->setEntityLabelInPlural('Relations médias')
            ->setDefaultSort(['datePublication' => 'DESC', 'createdAt' => 'DESC'])
            ->setPageTitle('index', "Gestion des relations médias")
            ->setPageTitle('new', 'Ajouter un article média')
            ->setPageTitle('edit', fn (RelationMedia $r) => sprintf('Modifier « %s »', $r->getTitre()))
            ->setSearchFields(['titre', 'nomDeMedia', 'typeMedia']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('titre', 'Titre de l\'article *')
            ->setColumns(6);

        yield TextField::new('nomDeMedia', 'Nom du média *')
            ->setColumns(6)
            ->setHelp('Ex: RFI, Radio Maendeleo, CongoWeb, etc.');

        yield TextField::new('typeMedia', 'Type de média')
            ->setColumns(6)
            ->setHelp('Ex: Journal, Radio, Télévision, Site Web, Blog');

        yield DateField::new('datePublication', 'Date de publication')
            ->setColumns(6)
            ->setHelp('Date à laquelle l\'article a été publié')
            ->setRequired(false);

        yield UrlField::new('lien', 'Lien vers l\'article *')
            ->setColumns(12)
            ->setHelp('URL complète vers l\'article original');

        yield TextareaField::new('description', 'Description/Résumé')
            ->setRequired(false)
            ->setHelp('Résumé de l\'article (affiché sur le site)')
            ->hideOnIndex();

        yield UrlField::new('logoMedia', 'Logo du média')
            ->setColumns(6)
            ->setRequired(false)
            ->setHelp('URL vers le logo du média (format PNG recommandé)');

        yield BooleanField::new('actif', 'Actif')
            ->setColumns(6)
            ->renderAsSwitch(true)
            ->setHelp('Article visible sur le site public');

        yield DateField::new('createdAt', 'Date de création')
            ->onlyOnDetail()
            ->setFormat('dd/MM/yyyy HH:mm');

        yield DateField::new('updatedAt', 'Dernière modification')
            ->onlyOnDetail()
            ->setFormat('dd/MM/yyyy HH:mm');
    }

    public function updateEntity(\Doctrine\ORM\EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setUpdatedAt(new \DateTimeImmutable());
        parent::updateEntity($entityManager, $entityInstance);
    }

    public function persistEntity(\Doctrine\ORM\EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
    }
}