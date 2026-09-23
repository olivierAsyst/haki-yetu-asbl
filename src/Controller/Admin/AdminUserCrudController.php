<?php

namespace App\Controller\Admin;

use App\Entity\AdminUser;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserCrudController extends AbstractCrudController
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public static function getEntityFqcn(): string
    {
        return AdminUser::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Administrateur')
            ->setEntityLabelInPlural('Administrateurs')
            ->setPageTitle('index', 'Comptes du back-office');
    }

    public function configureFields(string $pageName): iterable
    {
        yield EmailField::new('email', 'E-mail (identifiant)');
        yield TextField::new('nomComplet', 'Nom complet');
        yield ArrayField::new('roles', 'Rôles')
            ->setHelp('ROLE_ADMIN est toujours appliqué automatiquement.')
            ->hideOnIndex();
        yield TextField::new('plainPassword', 'Mot de passe')
            ->setFormType(\Symfony\Component\Form\Extension\Core\Type\PasswordType::class)
            ->setFormTypeOptions(['mapped' => false, 'required' => 'new' === $pageName])
            ->onlyOnForms()
            ->setHelp('Laisser vide pour ne pas changer le mot de passe (en édition).');
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->hashPassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->hashPassword($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function hashPassword(AdminUser $user): void
    {
        $form = $this->getContext()?->getRequest()?->request->all()['AdminUser'] ?? null;
        $plainPassword = $form['plainPassword'] ?? null;

        if (!empty($plainPassword)) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        } elseif (null === $user->getPassword()) {
            // Sécurité : un utilisateur ne doit jamais être créé sans mot de passe.
            $user->setPassword($this->passwordHasher->hashPassword($user, bin2hex(random_bytes(16))));
        }
    }
}
