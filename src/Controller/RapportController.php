<?php

namespace App\Controller;

use App\Entity\Rapport;
use App\Form\RapportType;
use App\Repository\RapportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rapports')]
class RapportController extends AbstractController
{
    #[Route('/', name: 'app_rapports_index', methods: ['GET'])]
    public function index(Request $request, RapportRepository $rapportRepository): Response
    {
        $filters = [
            'annee' => $request->query->get('annee'),
            'type' => $request->query->get('type'),
            'langue' => $request->query->get('langue'),
            'search' => $request->query->get('search'),
        ];

        $rapports = $rapportRepository->findByFilters($filters);

        return $this->render('rapports/index.html.twig', [
            'rapports' => $rapports,
            'filters' => $filters,
            'annees' => $rapportRepository->findDistinctAnnees(),
            'types' => $rapportRepository->findDistinctTypes(),
            'langues' => $rapportRepository->findDistinctLangues(),
        ]);
    }

    #[Route('/new', name: 'app_rapports_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $rapport = new Rapport();
        $form = $this->createForm(RapportType::class, $rapport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rapport);
            $entityManager->flush();

            $this->addFlash('success', 'Le rapport a été créé avec succès.');

            return $this->redirectToRoute('app_rapports_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rapports/new.html.twig', [
            'rapport' => $rapport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rapports_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Rapport $rapport): Response
    {
        return $this->render('rapports/show.html.twig', [
            'rapport' => $rapport,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rapports_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Rapport $rapport, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(RapportType::class, $rapport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le rapport a été modifié avec succès.');

            return $this->redirectToRoute('app_rapports_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rapports/edit.html.twig', [
            'rapport' => $rapport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rapports_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Rapport $rapport, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$rapport->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($rapport);
            $entityManager->flush();

            $this->addFlash('success', 'Le rapport a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_rapports_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/download', name: 'app_rapports_download', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function download(Rapport $rapport): Response
    {
        $filePath = $this->getParameter('rapports_directory') . '/' . $rapport->getFichierName();
        
        if (!file_exists($filePath)) {
            $this->addFlash('error', 'Le fichier n\'existe pas.');
            return $this->redirectToRoute('app_rapports_index');
        }

        return $this->file($filePath);
    }
}