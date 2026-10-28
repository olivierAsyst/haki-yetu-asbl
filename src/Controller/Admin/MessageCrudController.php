<?php

namespace App\Controller\Admin;

use App\Entity\Message;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MessageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Message::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Message')
            ->setEntityLabelInPlural('Messages reçus')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPageTitle('index', 'Messages reçus via le formulaire de contact');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW)
            ->update(Action::INDEX, Action::DELETE, fn (Action $action) => $action->setLabel(''));
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nom', 'Nom');
        yield EmailField::new('email', 'E-mail');
        yield TextField::new('sujet', 'Sujet')->setRequired(false);
        yield TextareaField::new('message', 'Message')
            ->hideOnIndex();
        yield BooleanField::new('lu', 'Lu')
            ->renderAsSwitch(true);
        yield DateTimeField::new('createdAt', 'Reçu le')
            ->hideOnForm()
            ->setFormat('dd/MM/yyyy HH:mm');
    }
}
