<?php

namespace App\Entity;

use App\Repository\UrgenceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UrgenceRepository::class)]
class Urgence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre est obligatoire")]
    #[Assert\Length(max: 255, maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères")]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description est obligatoire")]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le type d'urgence est obligatoire")]
    private ?string $type = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le lieu est obligatoire")]
    private ?string $lieu = null;

    // Montants en DECIMAL (chaînes côté PHP, ex. "5000000.00") : pas d'erreurs d'arrondi
    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    #[Assert\NotNull(message: "Le montant cible est obligatoire")]
    #[Assert\Positive(message: "Le montant doit être positif")]
    private ?string $montantCible = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    #[Assert\NotNull(message: "Le montant collecté est obligatoire")]
    #[Assert\PositiveOrZero(message: "Le montant collecté doit être positif ou zéro")]
    private ?string $montantCollecte = '0.00';

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de début est obligatoire")]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column]
    private bool $estActive = true;

    #[ORM\Column(length: 20)]
    private ?string $statut = 'en_cours';

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->montantCollecte = '0.00';
        $this->estActive = true;
        $this->statut = 'en_cours';
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getMontantCible(): ?string
    {
        return $this->montantCible;
    }

    public function setMontantCible(?string $montantCible): static
    {
        $this->montantCible = $montantCible;

        return $this;
    }

    public function getMontantCollecte(): ?string
    {
        return $this->montantCollecte;
    }

    public function setMontantCollecte(?string $montantCollecte): static
    {
        // Champ facultatif dans le formulaire : vide = 0
        $this->montantCollecte = $montantCollecte ?? '0.00';

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function isEstActive(): bool
    {
        return $this->estActive;
    }

    public function setEstActive(bool $estActive): static
    {
        $this->estActive = $estActive;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getPourcentageCollecte(): int
    {
        if ((float) $this->montantCible <= 0) {
            return 0;
        }

        return (int) min(100, ((float) $this->montantCollecte / (float) $this->montantCible) * 100);
    }

    /**
     * Nombre de jours avant la date de fin : 0 si elle est passée, null si aucune date de fin.
     */
    public function getJoursRestants(): ?int
    {
        if (!$this->dateFin) {
            return null;
        }

        $aujourdhui = new \DateTimeImmutable('today');
        $fin = \DateTimeImmutable::createFromInterface($this->dateFin)->setTime(0, 0);

        if ($fin < $aujourdhui) {
            return 0;
        }

        return $aujourdhui->diff($fin)->days;
    }

    /**
     * Ajoute (ou retire, si négatif) un montant au total collecté, sans descendre sous zéro.
     */
    public function ajouterAuMontantCollecte(string $montant): static
    {
        $total = max(0, (float) $this->montantCollecte + (float) $montant);
        $this->montantCollecte = number_format($total, 2, '.', '');
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }
}
