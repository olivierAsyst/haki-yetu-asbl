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
use Doctrine\ORM\EntityManagerInterface;

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
            ->setHelp('index', 'Programme, activité, réunion... Seules les annonces « actives » apparaissent sur le site.');
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

        yield ChoiceField::new('dureeAffichage', 'Durée d\'affichage')
            ->setChoices(Annonce::DUREES)
            ->setHelp('L\'annonce sera automatiquement masquée après cette durée');

        yield DateTimeField::new('expireAt', 'Expire le')
            ->hideOnForm()
            ->hideOnIndex();

        yield BooleanField::new('actif', 'Visible sur le site')
            ->renderAsSwitch(true);
    }

    private function calculateExpireAt(string $duree): \DateTimeImmutable
    {
        switch ($duree) {
            case '6h':
                return (new \DateTimeImmutable())->modify('+6 hours');
            case '24h':
                return (new \DateTimeImmutable())->modify('+24 hours');
            case '3jours':
                return (new \DateTimeImmutable())->modify('+3 days');
            case '1semaine':
                return (new \DateTimeImmutable())->modify('+1 week');
            case '1mois':
                return (new \DateTimeImmutable())->modify('+1 month');
            default:
                return (new \DateTimeImmutable())->modify('+1 week');
        }
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance->getDureeAffichage() && !$entityInstance->getExpireAt()) {
            $entityInstance->setExpireAt($this->calculateExpireAt($entityInstance->getDureeAffichage()));
        }
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance->getDureeAffichage()) {
            $entityInstance->setExpireAt($this->calculateExpireAt($entityInstance->getDureeAffichage()));
        }
        parent::updateEntity($entityManager, $entityInstance);
    }
}
