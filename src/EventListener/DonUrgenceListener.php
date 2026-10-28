<?php

namespace App\EventListener;

use App\Entity\Don;
use App\Entity\Urgence;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;

/**
 * Tient à jour Urgence::montantCollecte à partir des dons qui lui sont rattachés.
 *
 * À chaque enregistrement, on retire la contribution de l'ancien état du don et on ajoute
 * celle du nouvel état. Cela couvre tous les cas : don payé, annulé, remboursé, montant
 * corrigé, urgence changée, don supprimé. Seuls les dons payés en CDF comptent
 * (voir Don::contributionUrgence()).
 */
#[AsDoctrineListener(event: Events::onFlush)]
class DonUrgenceListener
{
    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        /** @var array<int, Urgence> $modifiees */
        $modifiees = [];

        foreach ($uow->getScheduledEntityInsertions() as $entite) {
            if ($entite instanceof Don) {
                $this->ajouter($uow, $entite->getUrgence(), Don::contributionUrgence($entite->getStatut(), $entite->getDevise(), $entite->getMontant()), $modifiees);
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entite) {
            if (!$entite instanceof Don) {
                continue;
            }

            $changements = $uow->getEntityChangeSet($entite);
            $ancien = static fn (string $champ, mixed $actuel): mixed => \array_key_exists($champ, $changements) ? $changements[$champ][0] : $actuel;

            $this->ajouter($uow, $ancien('urgence', $entite->getUrgence()), -Don::contributionUrgence(
                $ancien('statut', $entite->getStatut()),
                $ancien('devise', $entite->getDevise()),
                $ancien('montant', $entite->getMontant()),
            ), $modifiees);
            $this->ajouter($uow, $entite->getUrgence(), Don::contributionUrgence($entite->getStatut(), $entite->getDevise(), $entite->getMontant()), $modifiees);
        }

        foreach ($uow->getScheduledEntityDeletions() as $entite) {
            if ($entite instanceof Don) {
                $this->ajouter($uow, $entite->getUrgence(), -Don::contributionUrgence($entite->getStatut(), $entite->getDevise(), $entite->getMontant()), $modifiees);
            }
        }

        $metadata = $em->getClassMetadata(Urgence::class);
        foreach ($modifiees as $urgence) {
            $uow->recomputeSingleEntityChangeSet($metadata, $urgence);
        }
    }

    /**
     * @param array<int, Urgence> $modifiees
     */
    private function ajouter(UnitOfWork $uow, ?Urgence $urgence, float $montant, array &$modifiees): void
    {
        if (null === $urgence || 0.0 === $montant || $uow->isScheduledForDelete($urgence) || !$uow->isInIdentityMap($urgence)) {
            return;
        }

        $urgence->ajouterAuMontantCollecte((string) $montant);
        $modifiees[spl_object_id($urgence)] = $urgence;
    }
}
