<?php

namespace App\Controller\Admin;

use App\Entity\Annonce;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * La date d'expiration est calculée par l'entité (Annonce::setDureeAffichage)
 * uniquement quand la durée change : aucune logique en double ici.
 */
class AnnonceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Annonce::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Annonce')
            ->setEntityLabelInPlural('Annonces')
            ->setDefaultSort(['actif' => 'DESC', 'dateEvenement' => 'ASC'])
            ->setPageTitle('index', "Annonces affichées sur la page d'accueil")
            ->setHelp('index', 'Programme, activité, réunion... Seules les annonces « actives » et non expirées apparaissent sur le site.');
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('titre', 'Titre');
        yield ChoiceField::new('type', 'Type')
            ->setChoices(Annonce::TYPES);
        yield TextareaField::new('contenu', 'Contenu')
            ->setRequired(false)
            ->hideOnIndex();
        yield DateField::new('dateEvenement', 'Date de l\'événement')
            ->setRequired(false);

        // Ordre important : « Expire le » est enregistré AVANT la durée. Si la durée change,
        // l'expiration est recalculée ; sinon la date saisie ici est conservée (pour prolonger).
        yield DateTimeField::new('expireAt', 'Expire le')
            ->setRequired(false)
            ->setFormat('dd/MM/yyyy HH:mm')
            ->setHelp('Modifiable pour prolonger ou raccourcir l\'affichage. Changer la durée ci-dessous recalcule cette date à partir de maintenant.')
            ->hideWhenCreating();
        yield ChoiceField::new('dureeAffichage', 'Durée d\'affichage')
            ->setChoices(Annonce::DUREES)
            ->setHelp('L\'annonce sera automatiquement masquée après cette durée.')
            ->hideOnIndex();

        yield BooleanField::new('actif', 'Visible sur le site')
            ->renderAsSwitch(true);
    }
}
