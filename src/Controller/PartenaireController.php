<?php

namespace App\Controller;

use App\Entity\Partenaire;
use App\Form\PartenaireType;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/partenaires')]
class PartenaireController extends AbstractController
{
    #[Route('/', name: 'app_partenaires', methods: ['GET'])]
    public function index(PartenaireRepository $partenaireRepository): Response
    {
        $partenaires = $partenaireRepository->findActifs();
        $categories = $partenaireRepository->findDistinctCategories();

        // Grouper par catégorie pour l'affichage
        $partenairesParCategorie = [];
        foreach ($partenaires as $partenaire) {
            $categorie = $partenaire->getCategorie() ?? 'Autres';
            $partenairesParCategorie[$categorie][] = $partenaire;
        }

        return $this->render('partenaires/index.html.twig', [
            'partenairesParCategorie' => $partenairesParCategorie,
            'categories' => $categories,
        ]);
    }

    #[Route('/admin', name: 'app_partenaires_admin', methods: ['GET'])]
    public function admin(PartenaireRepository $partenaireRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $partenaires = $partenaireRepository->findAll();

        return $this->render('partenaires/admin.html.twig', [
            'partenaires' => $partenaires,
        ]);
    }

    #[Route('/admin/new', name: 'app_partenaires_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $partenaire = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($partenaire);
            $entityManager->flush();

            $this->addFlash('success', 'Le partenaire a été créé avec succès.');

            return $this->redirectToRoute('app_partenaires_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('partenaires/new.html.twig', [
            'partenaire' => $partenaire,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{id}/edit', name: 'app_partenaires_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Partenaire $partenaire, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le partenaire a été modifié avec succès.');

            return $this->redirectToRoute('app_partenaires_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('partenaires/edit.html.twig', [
            'partenaire' => $partenaire,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{id}', name: 'app_partenaires_delete', methods: ['POST'])]
    public function delete(Request $request, Partenaire $partenaire, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$partenaire->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($partenaire);
            $entityManager->flush();

            $this->addFlash('success', 'Le partenaire a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_partenaires_admin', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/{id}/toggle', name: 'app_partenaires_toggle', methods: ['POST'])]
    public function toggleActif(Request $request, Partenaire $partenaire, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('toggle'.$partenaire->getId(), $request->getPayload()->get('_token'))) {
            $partenaire->setActif(!$partenaire->isActif());
            $entityManager->flush();

            $status = $partenaire->isActif() ? 'activé' : 'désactivé';
            $this->addFlash('success', "Le partenaire a été $status avec succès.");
        }

        return $this->redirectToRoute('app_partenaires_admin', [], Response::HTTP_SEE_OTHER);
    }
}