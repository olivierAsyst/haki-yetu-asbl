<?php

namespace App\Controller;

use App\Entity\RelationMedia;
use App\Form\RelationMediaType;
use App\Repository\RelationMediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/relation-media')]
class RelationMediaController extends AbstractController
{
    #[Route('/', name: 'app_relation_media')]
    public function index(RelationMediaRepository $relationMediaRepository): Response
    {
        $relationMedia = $relationMediaRepository->findAllOrderedByPublicationDate();
        $distinctMedia = $relationMediaRepository->findDistinctMedia();

        return $this->render('relation_media/index.html.twig', [
            'relation_media' => $relationMedia,
            'distinct_media' => $distinctMedia,
        ]);
    }

    #[Route('/new', name: 'app_relation_media_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réservé aux administrateurs.');
        }

        $relationMedia = new RelationMedia();
        $form = $this->createForm(RelationMediaType::class, $relationMedia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $relationMedia->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($relationMedia);
            $entityManager->flush();

            $this->addFlash('success', 'L\'article média a été ajouté avec succès.');

            return $this->redirectToRoute('app_relation_media', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('relation_media/new.html.twig', [
            'relation_media' => $relationMedia,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_relation_media_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(RelationMedia $relationMedia): Response
    {
        return $this->render('relation_media/show.html.twig', [
            'relation_media' => $relationMedia,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_relation_media_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, RelationMedia $relationMedia, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réservé aux administrateurs.');
        }

        $form = $this->createForm(RelationMediaType::class, $relationMedia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $relationMedia->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'L\'article média a été modifié avec succès.');

            return $this->redirectToRoute('app_relation_media', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('relation_media/edit.html.twig', [
            'relation_media' => $relationMedia,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_relation_media_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, RelationMedia $relationMedia, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réservé aux administrateurs.');
        }

        if ($this->isCsrfTokenValid('delete'.$relationMedia->getId(), $request->request->get('_token'))) {
            $entityManager->remove($relationMedia);
            $entityManager->flush();

            $this->addFlash('success', 'L\'article média a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_relation_media', [], Response::HTTP_SEE_OTHER);
    }
}