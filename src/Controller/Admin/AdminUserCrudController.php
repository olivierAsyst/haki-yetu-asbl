<?php

namespace App\Controller\Admin;

use App\Entity\AdminUser;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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

        // Pas de champ « rôles » : tous les comptes ont ROLE_ADMIN (voir AdminUser::getRoles()),
        // et un champ libre permettait de s'attribuer n'importe quel rôle.
        $contraintes = [new Length(min: 8, minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.')];
        if (Crud::PAGE_NEW === $pageName) {
            $contraintes[] = new NotBlank(message: 'Le mot de passe est obligatoire.');
        }

        yield TextField::new('plainPassword', 'Mot de passe')
            ->setFormType(PasswordType::class)
            ->setFormTypeOptions([
                'mapped' => false,
                'required' => Crud::PAGE_NEW === $pageName,
                'constraints' => $contraintes,
            ])
            ->onlyOnForms()
            ->setHelp('8 caractères minimum. En modification, laisser vide pour garder le mot de passe actuel.');
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

    /**
     * Appelé pour la suppression simple et la suppression groupée.
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance === $this->getUser()) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer votre propre compte.');

            return;
        }

        if ($entityManager->getRepository(AdminUser::class)->count([]) <= 1) {
            $this->addFlash('danger', 'Impossible de supprimer le dernier compte administrateur.');

            return;
        }

        parent::deleteEntity($entityManager, $entityInstance);
    }

    private function hashPassword(AdminUser $user): void
    {
        $plainPassword = $this->getContext()?->getRequest()?->request->all()['AdminUser']['plainPassword'] ?? null;

        if (\is_string($plainPassword) && '' !== $plainPassword) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        }
    }
}
