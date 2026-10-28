<?php

namespace App\Security;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;

/**
 * Limite le nombre d'envois des formulaires publics par adresse IP
 * (voir config/packages/rate_limiter.yaml).
 */
class AntiSpam
{
    public function __construct(
        private readonly RateLimiterFactoryInterface $formulairePublicLimiter,
    ) {
    }

    /**
     * À appeler juste après handleRequest() : si la limite est atteinte,
     * ajoute une erreur au formulaire, qui ne sera donc pas valide.
     */
    public function verifier(FormInterface $form, Request $request): void
    {
        if (!$form->isSubmitted()) {
            return;
        }

        $cle = ($request->attributes->getString('_route') ?: 'formulaire').'-'.($request->getClientIp() ?? 'inconnu');

        if (!$this->formulairePublicLimiter->create($cle)->consume()->isAccepted()) {
            $form->addError(new FormError('Vous avez envoyé trop de formulaires en peu de temps. Merci de réessayer dans quelques minutes.'));
        }
    }
}
