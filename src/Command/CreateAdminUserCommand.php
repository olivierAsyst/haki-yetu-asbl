<?php

namespace App\Command;

use App\Entity\AdminUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Crée (ou met à jour) un compte administrateur pour le back-office.',
)]
class CreateAdminUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $io->ask('Adresse e-mail de connexion', 'admin@hakiyetu.org');
        $nomComplet = $io->ask('Nom complet', 'Administrateur HAKI YETU');
        $password = $io->askHidden('Mot de passe (ne sera pas affiché)');
        // 000000000

        if (empty($password) || \strlen($password) < 8) {
            $io->error('Le mot de passe doit contenir au moins 8 caractères.');

            return Command::FAILURE;
        }

        $repository = $this->entityManager->getRepository(AdminUser::class);
        $user = $repository->findOneBy(['email' => $email]) ?? new AdminUser();

        $user->setEmail($email);
        $user->setNomComplet($nomComplet);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('Compte administrateur prêt : %s', $email));

        return Command::SUCCESS;
    }
}
