<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AnnonceRepository::class)]
class Annonce
{
    public const TYPE_PROGRAMME = 'programme';
    public const TYPE_ACTIVITE = 'activite';
    public const TYPE_REUNION = 'reunion';
    public const TYPE_AUTRE = 'autre';

    public const TYPES = [
        'Programme' => self::TYPE_PROGRAMME,
        'Activité' => self::TYPE_ACTIVITE,
        'Réunion' => self::TYPE_REUNION,
        'Autre' => self::TYPE_AUTRE,
    ];

    public const DUREE_6H = '6h';
    public const DUREE_24H = '24h';
    public const DUREE_3JOURS = '3jours';
    public const DUREE_1SEMAINE = '1semaine';
    public const DUREE_1MOIS = '1mois';

    public const DUREES = [
        '6 heures' => self::DUREE_6H,
        '24 heures' => self::DUREE_24H,
        '3 jours' => self::DUREE_3JOURS,
        '1 semaine' => self::DUREE_1SEMAINE,
        '1 mois' => self::DUREE_1MOIS,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre de l'annonce est obligatoire.")]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $contenu = null;

    #[ORM\Column(length: 30)]
    #[Assert\Choice(choices: [self::TYPE_PROGRAMME, self::TYPE_ACTIVITE, self::TYPE_REUNION, self::TYPE_AUTRE])]
    private string $type = self::TYPE_AUTRE;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateEvenement = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Choice(choices: [self::DUREE_6H, self::DUREE_24H, self::DUREE_3JOURS, self::DUREE_1SEMAINE, self::DUREE_1MOIS])]
    private ?string $dureeAffichage = self::DUREE_1SEMAINE;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expireAt = null;

    #[ORM\Column]
    private bool $actif = true;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->dureeAffichage = self::DUREE_1SEMAINE;
        $this->expireAt = (new \DateTimeImmutable())->modify('+1 week');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(?string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getTypeLabel(): string
    {
        return array_flip(self::TYPES)[$this->type] ?? 'Autre';
    }

    public function getDateEvenement(): ?\DateTimeImmutable
    {
        return $this->dateEvenement;
    }

    public function setDateEvenement(?\DateTimeImmutable $dateEvenement): static
    {
        $this->dateEvenement = $dateEvenement;

        return $this;
    }

    public function getDureeAffichage(): ?string
    {
        return $this->dureeAffichage;
    }

    public function setDureeAffichage(?string $dureeAffichage): static
    {
        // On ne recalcule l'expiration que si la durée change vraiment : un formulaire
        // d'édition renvoie toujours la durée actuelle, ce qui remettait l'expiration à zéro.
        if ($dureeAffichage === $this->dureeAffichage) {
            return $this;
        }

        $this->dureeAffichage = $dureeAffichage;
        if ($dureeAffichage) {
            $this->updateExpireAt();
        }

        return $this;
    }

    public function getDureeLabel(): string
    {
        return array_flip(self::DUREES)[$this->dureeAffichage ?? self::DUREE_1SEMAINE] ?? '1 semaine';
    }

    public function getExpireAt(): ?\DateTimeImmutable
    {
        return $this->expireAt;
    }

    public function setExpireAt(?\DateTimeImmutable $expireAt): static
    {
        $this->expireAt = $expireAt;

        return $this;
    }

    public function isExpired(): bool
    {
        return $this->expireAt !== null && $this->expireAt < new \DateTimeImmutable();
    }

    private function updateExpireAt(): void
    {
        if (!$this->dureeAffichage) {
            return;
        }

        switch ($this->dureeAffichage) {
            case self::DUREE_6H:
                $this->expireAt = (new \DateTimeImmutable())->modify('+6 hours');
                break;
            case self::DUREE_24H:
                $this->expireAt = (new \DateTimeImmutable())->modify('+24 hours');
                break;
            case self::DUREE_3JOURS:
                $this->expireAt = (new \DateTimeImmutable())->modify('+3 days');
                break;
            case self::DUREE_1SEMAINE:
                $this->expireAt = (new \DateTimeImmutable())->modify('+1 week');
                break;
            case self::DUREE_1MOIS:
                $this->expireAt = (new \DateTimeImmutable())->modify('+1 month');
                break;
            default:
                $this->expireAt = (new \DateTimeImmutable())->modify('+1 week');
                break;
        }
    }

    public function isActif(): bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function __toString(): string
    {
        return $this->titre ?? '';
    }
}
