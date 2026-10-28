<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\OffreEmploi;
use App\Form\CandidatureType;
use App\Repository\CandidatureRepository;
use App\Security\AntiSpam;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;

class CandidatureController extends AbstractController
{
    public function __construct(
        private readonly CandidatureRepository $candidatureRepository,
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $logger,
        private readonly AntiSpam $antiSpam,
        #[Autowire('%env(CONTACT_RECIPIENT_EMAIL)%')]
        private readonly string $recipientEmail,
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
        $this->antiSpam->verifier($form, $request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->candidatureRepository->save($candidature, true);
            } catch (\Throwable $e) {
                $this->logger->error('Enregistrement de la candidature impossible', ['exception' => $e]);
                $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi de votre candidature. Veuillez réessayer.');

                return $this->render('candidature/new.html.twig', [
                    'offre' => $offre,
                    'form' => $form,
                ], new Response(null, Response::HTTP_INTERNAL_SERVER_ERROR));
            }

            // La notification ne doit pas bloquer la candidature, déjà enregistrée
            try {
                $email = (new TemplatedEmail())
                    // Même adresse en expéditeur et destinataire pour passer les contrôles SPF/DMARC du SMTP
                    ->from(new Address($this->recipientEmail, 'Site HAKI YETU asbl'))
                    ->to($this->recipientEmail)
                    ->replyTo(new Address($candidature->getEmail(), $candidature->getPrenom().' '.$candidature->getNom()))
                    ->subject('[Site web] Nouvelle candidature : '.$candidature->getPrenom().' '.$candidature->getNom().' — '.$offre->getTitre())
                    ->htmlTemplate('emails/candidature_notification.html.twig')
                    ->context([
                        'candidature' => $candidature,
                        'offre' => $offre,
                    ]);

                $this->mailer->send($email);
            } catch (\Throwable $e) {
                $this->logger->error('Envoi de la notification de candidature impossible', ['exception' => $e]);
            }

            $this->addFlash('success', 'Votre candidature a été envoyée avec succès. Nous vous contacterons si votre profil correspond au poste.');

            return $this->redirectToRoute('app_emplois_show', ['slug' => $offre->getSlug()], Response::HTTP_SEE_OTHER);
        }

        // Passer le formulaire (et non createView) renvoie un code 422 en cas d'erreur, attendu par Turbo
        return $this->render('candidature/new.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }
}
