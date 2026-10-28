<?php

namespace App\Controller;

use App\Entity\Urgence;
use App\Form\UrgenceType;
use App\Repository\UrgenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/urgences')]
class UrgenceController extends AbstractController
{
    #[Route('/', name: 'app_urgence_index', methods: ['GET'])]
    public function index(UrgenceRepository $urgenceRepository): Response
    {
        $urgencesActives = $urgenceRepository->findActiveUrgences();
        $urgencesArchivees = $urgenceRepository->findArchivedUrgences();

        return $this->render('urgence/index.html.twig', [
            'urgences_actives' => $urgencesActives,
            'urgences_archivees' => $urgencesArchivees,
        ]);
    }

    #[Route('/{id}', name: 'app_urgence_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Urgence $urgence): Response
    {
        return $this->render('urgence/show.html.twig', [
            'urgence' => $urgence,
        ]);
    }

    #[Route('/{id}/don', name: 'app_urgence_don', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function don(Urgence $urgence): Response
    {
        return $this->redirectToRoute('app_don', [
            'urgence' => $urgence->getId(),
            'montant_suggestion' => 50000,
        ], Response::HTTP_SEE_OTHER);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_urgence_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $urgence = new Urgence();
        $form = $this->createForm(UrgenceType::class, $urgence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($urgence);
            $entityManager->flush();

            $this->addFlash('success', 'L\'appel d\'urgence a été créé avec succès.');

            return $this->redirectToRoute('app_urgence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('urgence/new.html.twig', [
            'urgence' => $urgence,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_urgence_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Urgence $urgence, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UrgenceType::class, $urgence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $urgence->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'L\'appel d\'urgence a été modifié avec succès.');

            return $this->redirectToRoute('app_urgence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('urgence/edit.html.twig', [
            'urgence' => $urgence,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_urgence_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Urgence $urgence, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$urgence->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($urgence);
            $entityManager->flush();

            $this->addFlash('success', 'L\'appel d\'urgence a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_urgence_index', [], Response::HTTP_SEE_OTHER);
    }
}