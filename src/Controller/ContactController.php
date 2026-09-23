<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        #[\Symfony\Component\DependencyInjection\Attribute\Autowire('%env(CONTACT_RECIPIENT_EMAIL)%')] string $contactRecipientEmail,
    ): Response {
        $message = new Message();
        $form = $this->createForm(ContactType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();

            try {
                $email = (new Email())
                    ->from(new Address($contactRecipientEmail, 'Site HAKI YETU asbl'))
                    ->to($contactRecipientEmail)
                    ->replyTo(new Address($message->getEmail(), $message->getNom()))
                    ->subject('[Site web] '.($message->getSujet() ?: 'Nouveau message de contact'))
                    ->htmlTemplate('emails/contact.html.twig')
                    ->context(['message' => $message]);

                $mailer->send($email);
            } catch (TransportExceptionInterface) {
                // Le message est déjà enregistré en base ; on informe simplement
                // que l'e-mail n'a pas pu partir, sans bloquer l'utilisateur.
                $this->addFlash('warning', "Votre message a bien été enregistré, mais l'envoi de l'e-mail de notification a échoué. Nous le consulterons tout de même depuis le back-office.");
            }

            $this->addFlash('success', 'Merci ! Votre message a bien été envoyé, nous vous répondrons dès que possible.');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form,
        ]);
    }
}
