<?php

namespace App\Controller;

use App\Entity\Don;
use App\Form\DonType;
use App\Repository\DonRepository;
use App\Repository\UrgenceRepository;
use App\Security\AntiSpam;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/don')]
class DonController extends AbstractController
{
    private const SESSION_DON_ID = 'don_confirmation_id';

    #[Route('/', name: 'app_don', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, AntiSpam $antiSpam, UrgenceRepository $urgenceRepository): Response
    {
        $don = new Don();

        // Don lancé depuis un appel d'urgence (/urgences/{id}/don) : on rattache et on pré-remplit.
        // Le paramètre reste dans l'URL lors de l'envoi du formulaire.
        $urgence = $request->query->getInt('urgence') > 0 ? $urgenceRepository->find($request->query->getInt('urgence')) : null;
        if ($urgence && $urgence->isEstActive()) {
            $don->setUrgence($urgence)
                ->setTypeDon('urgence')
                ->setDevise('CDF');

            $suggestion = $request->query->getInt('montant_suggestion');
            if ($suggestion > 0) {
                $don->setMontant((string) $suggestion);
            }
        }

        $form = $this->createForm(DonType::class, $don);
        $form->handleRequest($request);
        $antiSpam->verifier($form, $request);

        if ($form->isSubmitted() && $form->isValid()) {
            $don->setStatut('en_attente');

            $entityManager->persist($don);
            $entityManager->flush();

            // L'id du don est gardé en session : la page de confirmation n'est visible
            // que par la personne qui vient de donner (pas d'id devinable dans l'URL).
            $request->getSession()->set(self::SESSION_DON_ID, $don->getId());

            $this->addFlash('success', 'Merci pour votre don ! Retrouvez ci-dessous les instructions de paiement.');

            return $this->redirectToRoute('app_don_confirmation', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('don/index.html.twig', [
            'form' => $form,
            'urgence' => $don->getUrgence(),
        ]);
    }

    #[Route('/confirmation', name: 'app_don_confirmation', methods: ['GET'])]
    public function confirmation(Request $request, DonRepository $donRepository): Response
    {
        $donId = $request->getSession()->get(self::SESSION_DON_ID);
        $don = $donId ? $donRepository->find($donId) : null;

        if (!$don) {
            return $this->redirectToRoute('app_don');
        }

        return $this->render('don/confirmation.html.twig', [
            'don' => $don,
        ]);
    }

    #[Route('/admin', name: 'app_don_admin', methods: ['GET'])]
    public function admin(DonRepository $donRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $totauxPayes = $donRepository->getTotauxParDevise('paye');

        return $this->render('don/admin.html.twig', [
            'dons' => $donRepository->findRecents(),
            'statistiques' => [
                // Toujours par devise : on n'additionne pas des USD et des CDF
                'total' => $totauxPayes,
                'en_attente' => $donRepository->getTotauxParDevise('en_attente'),
                'nombre_total' => $donRepository->count([]),
                'nombre_payes' => $donRepository->count(['statut' => 'paye']),
                'par_type' => $this->ajouterPourcentages($donRepository->getStatistiquesParTypeDon(), $totauxPayes),
                'par_methode' => $this->ajouterPourcentages($donRepository->getStatistiquesParMethodePaiement(), $totauxPayes),
            ],
        ]);
    }

    #[Route('/admin/{id}', name: 'app_don_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Don $don): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('don/show.html.twig', [
            'don' => $don,
        ]);
    }

    #[Route('/admin/{id}/edit', name: 'app_don_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Don $don, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(DonType::class, $don, ['admin' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le don a été modifié avec succès.');
            $this->avertirSiNonComptabilise($don);

            return $this->redirectToRoute('app_don_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('don/edit.html.twig', [
            'don' => $don,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{id}', name: 'app_don_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Don $don, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$don->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($don);
            $entityManager->flush();

            $this->addFlash('success', 'Le don a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_don_admin', [], Response::HTTP_SEE_OTHER);
    }

    // Seuls les statuts connus sont acceptés dans l'URL
    #[Route('/admin/{id}/statut/{statut}', name: 'app_don_changer_statut', requirements: ['id' => '\d+', 'statut' => 'en_attente|confirme|paye|annule|rembourse'], methods: ['POST'])]
    public function changerStatut(Request $request, Don $don, string $statut, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('statut'.$don->getId(), $request->getPayload()->getString('_token'))) {
            $don->setStatut($statut);
            $entityManager->flush();

            $this->addFlash('success', sprintf('Le statut du don a été changé en "%s".', $don->getStatutLabel()));
            $this->avertirSiNonComptabilise($don);
        }

        return $this->redirectToRoute('app_don_show', ['id' => $don->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * Un don payé pour une urgence n'est ajouté automatiquement au montant collecté
     * que s'il est en CDF (devise des objectifs d'urgence). Sinon on prévient l'admin.
     */
    private function avertirSiNonComptabilise(Don $don): void
    {
        if ($don->isPaye() && $don->getUrgence() && !$don->compteDansUrgence()) {
            $this->addFlash('warning', sprintf(
                'Ce don est en %s : il n\'a pas été ajouté automatiquement au montant collecté de l\'urgence « %s » (suivi en CDF). Mettez ce montant à jour manuellement après conversion.',
                $don->getDevise(),
                $don->getUrgence()->getTitre()
            ));
        }
    }

    /**
     * Ajoute à chaque ligne sa part (en %) du total payé dans la même devise.
     *
     * @param list<array<string, mixed>> $lignes
     * @param array<string, string>      $totauxParDevise
     *
     * @return list<array<string, mixed>>
     */
    private function ajouterPourcentages(array $lignes, array $totauxParDevise): array
    {
        foreach ($lignes as &$ligne) {
            $total = (float) ($totauxParDevise[$ligne['devise']] ?? 0);
            $ligne['pourcentage'] = $total > 0 ? round((float) $ligne['total'] / $total * 100, 1) : 0;
        }

        return $lignes;
    }
}
