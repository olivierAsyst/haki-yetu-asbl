<?php

namespace App\Entity;

use App\Repository\DonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DonRepository::class)]
class Don
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire.')]
    #[Assert\Email(message: 'L\'email n\'est pas valide.')]
    private ?string $email = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $entreprise = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le montant est obligatoire.')]
    #[Assert\Positive(message: 'Le montant doit être positif.')]
    private ?float $montant = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'La devise est obligatoire.')]
    private ?string $devise = 'USD';

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le type de don est obligatoire.')]
    private ?string $typeDon = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'La méthode de paiement est obligatoire.')]
    private ?string $methodePaiement = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column]
    private bool $recevoirNewsletter = false;

    #[ORM\Column]
    private bool $anonyme = false;

    #[ORM\Column]
    private bool $receiptDemande = false;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $statut = 'en_attente';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $referencePaiement = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $datePaiement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $pays = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $typePersonne = 'particulier';

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEntreprise(): ?string
    {
        return $this->entreprise;
    }

    public function setEntreprise(?string $entreprise): static
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDevise(): ?string
    {
        return $this->devise;
    }

    public function setDevise(string $devise): static
    {
        $this->devise = $devise;

        return $this;
    }

    public function getTypeDon(): ?string
    {
        return $this->typeDon;
    }

    public function setTypeDon(string $typeDon): static
    {
        $this->typeDon = $typeDon;

        return $this;
    }

    public function getMethodePaiement(): ?string
    {
        return $this->methodePaiement;
    }

    public function setMethodePaiement(string $methodePaiement): static
    {
        $this->methodePaiement = $methodePaiement;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function isRecevoirNewsletter(): bool
    {
        return $this->recevoirNewsletter;
    }

    public function setRecevoirNewsletter(bool $recevoirNewsletter): static
    {
        $this->recevoirNewsletter = $recevoirNewsletter;

        return $this;
    }

    public function isAnonyme(): bool
    {
        return $this->anonyme;
    }

    public function setAnonyme(bool $anonyme): static
    {
        $this->anonyme = $anonyme;

        return $this;
    }

    public function isReceiptDemande(): bool
    {
        return $this->receiptDemande;
    }

    public function setReceiptDemande(bool $receiptDemande): static
    {
        $this->receiptDemande = $receiptDemande;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getReferencePaiement(): ?string
    {
        return $this->referencePaiement;
    }

    public function setReferencePaiement(?string $referencePaiement): static
    {
        $this->referencePaiement = $referencePaiement;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getDatePaiement(): ?\DateTimeImmutable
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(?\DateTimeImmutable $datePaiement): static
    {
        $this->datePaiement = $datePaiement;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getTypePersonne(): ?string
    {
        return $this->typePersonne;
    }

    public function setTypePersonne(?string $typePersonne): static
    {
        $this->typePersonne = $typePersonne;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('Don #%d - %s %s', $this->id, $this->montant, $this->devise);
    }

    public function getMontantFormate(): string
    {
        return number_format($this->montant, 2, ',', ' ') . ' ' . $this->devise;
    }

    public function getTypeDonLabel(): string
    {
        $labels = [
            'unique' => 'Don unique',
            'mensuel' => 'Don mensuel',
            'trimestriel' => 'Don trimestriel',
            'annuel' => 'Don annuel',
            'projet' => 'Don pour un projet spécifique',
            'urgence' => 'Don d\'urgence',
        ];

        return $labels[$this->typeDon] ?? $this->typeDon;
    }

    public function getMethodePaiementLabel(): string
    {
        $labels = [
            'mobile_money' => 'Mobile Money (M-Pesa, Airtel Money, Orange Money)',
            'virement_bancaire' => 'Virement bancaire',
            'wave' => 'Wave',
            'western_union' => 'Western Union',
            'moneygram' => 'MoneyGram',
            'especes' => 'Espèces',
            'autre' => 'Autre',
        ];

        return $labels[$this->methodePaiement] ?? $this->methodePaiement;
    }

    public function getStatutLabel(): string
    {
        $labels = [
            'en_attente' => 'En attente',
            'confirme' => 'Confirmé',
            'paye' => 'Payé',
            'annule' => 'Annulé',
            'rembourse' => 'Remboursé',
        ];

        return $labels[$this->statut] ?? $this->statut;
    }

    public function getStatutCouleur(): string
    {
        $couleurs = [
            'en_attente' => 'warning',
            'confirme' => 'info',
            'paye' => 'success',
            'annule' => 'danger',
            'rembourse' => 'secondary',
        ];

        return $couleurs[$this->statut] ?? 'secondary';
    }
}