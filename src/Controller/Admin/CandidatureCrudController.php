<?php

namespace App\Controller\Admin;

use App\Entity\Candidature;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * Candidatures reçues : consultation seule (pas de création ni de modification).
 * Les pièces jointes sont stockées hors de public/ et servies uniquement aux admins.
 */
class CandidatureCrudController extends AbstractCrudController
{
    /** Type de pièce dans l'URL => [champ Vich, nom de fichier en base] */
    private const FICHIERS = [
        'cv' => ['cvFileFile', 'getCvFile'],
        'lettre' => ['lettreMotivationFileFile', 'getLettreMotivationFile'],
        'autres' => ['autresDocumentsFile', 'getAutresDocuments'],
    ];

    public static function getEntityFqcn(): string
    {
        return Candidature::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Candidature')
            ->setEntityLabelInPlural('Candidatures')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPageTitle('index', 'Candidatures reçues')
            ->setPageTitle('detail', fn (Candidature $c) => sprintf('Candidature de %s %s', $c->getPrenom(), $c->getNom()))
            ->setSearchFields(['nom', 'prenom', 'email', 'telephone']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::EDIT);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nom', 'Nom');
        yield TextField::new('prenom', 'Prénom');
        yield EmailField::new('email', 'Email');
        yield TelephoneField::new('telephone', 'Téléphone');
        yield TextField::new('adresse', 'Adresse')->hideOnIndex();
        yield AssociationField::new('offreEmploi', 'Offre postulée');
        yield DateTimeField::new('createdAt', 'Reçue le')->setFormat('dd/MM/yyyy HH:mm');

        yield $this->champFichier('cvFile', 'CV', 'cv');
        yield $this->champFichier('lettreMotivationFile', 'Lettre de motivation', 'lettre');
        yield $this->champFichier('autresDocuments', 'Autres documents', 'autres');

        yield TextareaField::new('message', 'Message')->onlyOnDetail();
    }

    #[Route('/admin/candidature-fichier/{id}/{type}', name: 'admin_candidature_fichier', requirements: ['id' => '\d+', 'type' => 'cv|lettre|autres'], methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function telecharger(Candidature $candidature, string $type, DownloadHandler $downloadHandler): Response
    {
        [$champ, $getter] = self::FICHIERS[$type];

        if (!$candidature->$getter()) {
            throw new NotFoundHttpException('Aucun fichier pour cette candidature.');
        }

        // Affiché dans le navigateur (PDF) plutôt que téléchargé de force
        $response = $downloadHandler->downloadObject($candidature, $champ, null, null, false);

        $extension = strtolower(pathinfo((string) $candidature->$getter(), PATHINFO_EXTENSION));
        $response->headers->set('Content-Type', MimeTypes::getDefault()->getMimeTypes($extension)[0] ?? 'application/octet-stream');
        // Données personnelles : pas de mise en cache par le navigateur ou un proxy
        $response->headers->set('Cache-Control', 'private, no-store');

        return $response;
    }

    private function champFichier(string $propriete, string $libelle, string $type): TextField
    {
        return TextField::new($propriete, $libelle)
            ->renderAsHtml()
            ->formatValue(function (?string $fichier, Candidature $candidature) use ($type): string {
                if (!$fichier) {
                    return '<span class="text-muted">Aucun fichier</span>';
                }

                $icone = match (strtolower(pathinfo($fichier, PATHINFO_EXTENSION))) {
                    'pdf' => 'fa-file-pdf',
                    'doc', 'docx' => 'fa-file-word',
                    'zip' => 'fa-file-zipper',
                    default => 'fa-file',
                };

                return sprintf(
                    '<a href="%s" target="_blank" rel="noopener"><i class="fas %s"></i> Ouvrir</a>',
                    htmlspecialchars($this->generateUrl('admin_candidature_fichier', ['id' => $candidature->getId(), 'type' => $type])),
                    $icone
                );
            });
    }
}
