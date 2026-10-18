<?php

namespace App\Controller;

use App\Entity\Don;
use App\Form\DonType;
use App\Repository\DonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/don')]
class DonController extends AbstractController
{
    #[Route('/', name: 'app_don', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $don = new Don();
        $form = $this->createForm(DonType::class, $don);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Définir le statut initial
            $don->setStatut('en_attente');
            
            $entityManager->persist($don);
            $entityManager->flush();

            // Envoyer un email de confirmation (à implémenter)
            // $this->envoyerEmailConfirmation($don);

            $this->addFlash('success', 'Merci pour votre don ! Nous vous avons envoyé un email de confirmation avec les instructions de paiement.');

            return $this->redirectToRoute('app_don_confirmation', ['id' => $don->getId()], Response::HTTP_SEE_OTHER);
        }

        // Statistiques pour la page
        $totalDons = 0; // À remplacer par des vraies statistiques si disponible
        $nombreDonateurs = 0;

        return $this->render('don/index.html.twig', [
            'form' => $form,
            'totalDons' => $totalDons,
            'nombreDonateurs' => $nombreDonateurs,
        ]);
    }

    #[Route('/confirmation/{id}', name: 'app_don_confirmation', methods: ['GET'])]
    public function confirmation(Don $don): Response
    {
        return $this->render('don/confirmation.html.twig', [
            'don' => $don,
        ]);
    }

    #[Route('/admin', name: 'app_don_admin', methods: ['GET'])]
    public function admin(DonRepository $donRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $dons = $donRepository->findRecents();
        $statistiques = [
            'total' => $donRepository->getTotalMontant('paye'),
            'en_attente' => $donRepository->getTotalMontant('en_attente'),
            'par_type' => $donRepository->getStatistiquesParTypeDon(),
            'par_methode' => $donRepository->getStatistiquesParMethodePaiement(),
        ];

        return $this->render('don/admin.html.twig', [
            'dons' => $dons,
            'statistiques' => $statistiques,
        ]);
    }

    #[Route('/admin/{id}', name: 'app_don_show', methods: ['GET'])]
    public function show(Don $don): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('don/show.html.twig', [
            'don' => $don,
        ]);
    }

    #[Route('/admin/{id}/edit', name: 'app_don_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Don $don, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(DonType::class, $don);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le don a été modifié avec succès.');

            return $this->redirectToRoute('app_don_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('don/edit.html.twig', [
            'don' => $don,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{id}', name: 'app_don_delete', methods: ['POST'])]
    public function delete(Request $request, Don $don, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$don->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($don);
            $entityManager->flush();

            $this->addFlash('success', 'Le don a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_don_admin', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/{id}/statut/{statut}', name: 'app_don_changer_statut', methods: ['POST'])]
    public function changerStatut(Request $request, Don $don, string $statut, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('statut'.$don->getId(), $request->getPayload()->get('_token'))) {
            $don->setStatut($statut);
            
            if ($statut === 'paye') {
                $don->setDatePaiement(new \DateTimeImmutable());
            }

            $entityManager->flush();

            $this->addFlash('success', sprintf('Le statut du don a été changé en "%s".', $don->getStatutLabel()));
        }

        return $this->redirectToRoute('app_don_show', ['id' => $don->getId()], Response::HTTP_SEE_OTHER);
    }

    private function envoyerEmailConfirmation(Don $don): void
    {
        // À implémenter avec le service de mail Symfony
        // Pour l'instant, c'est une méthode placeholder
    }
}