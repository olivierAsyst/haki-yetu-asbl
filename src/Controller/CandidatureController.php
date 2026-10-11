<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\OffreEmploi;
use App\Form\CandidatureType;
use App\Repository\OffreEmploiRepository;
use App\Repository\CandidatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

class CandidatureController extends AbstractController
{
    public function __construct(
        private readonly OffreEmploiRepository $offreEmploiRepository,
        private readonly CandidatureRepository $candidatureRepository,
        private readonly MailerInterface $mailer
    ) {
    }

    #[Route('/emplois/{slug}/postuler', name: 'app_candidature_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])] OffreEmploi $offre
    ): Response {
        if (!$offre->isActif() || $offre->isExpired()) {
            throw $this->createNotFoundException('Cette offre n\'est plus disponible.');
        }

        if ($offre->isFileExpired()) {
            throw $this->createNotFoundException('La date limite de candidature est dépassée.');
        }

        $candidature = new Candidature();
        $candidature->setOffreEmploi($offre);

        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->candidatureRepository->save($candidature, true);

                // Send notification email (optional, won't block if fails)
                try {
                    $email = (new TemplatedEmail())
                        ->from('noreply@hakiyetuasbl.cd')
                        ->to('moiserukabo@gmail.com')
                        ->subject('Nouvelle candidature : ' . $candidature->getNom() . ' ' . $candidature->getPrenom())
                        ->htmlTemplate('emails/candidature_notification.html.twig')
                        ->context([
                            'candidature' => $candidature,
                            'offre' => $offre,
                        ]);

                    $this->mailer->send($email);
                } catch (\Exception $e) {
                    // Log error but don't prevent the application from being saved
                    error_log('Email sending failed: ' . $e->getMessage());
                }

                $this->addFlash('success', 'Votre candidature a été envoyée avec succès. Nous vous contacterons si votre profil correspond au poste.');

                return $this->redirectToRoute('app_emplois_show', ['slug' => $offre->getSlug()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi de votre candidature. Veuillez réessayer.');
                error_log('Candidature save failed: ' . $e->getMessage());
            }
        }

        return $this->render('candidature/new.html.twig', [
            'offre' => $offre,
            'form' => $form->createView(),
        ]);
    }
}
