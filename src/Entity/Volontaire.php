<?php

namespace App\Entity;

use App\Repository\VolontaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VolontaireRepository::class)]
class Volontaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire")]
    #[Assert\Length(min: 2, max: 100, 
        minMessage: "Le prénom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères")]
    private ?string $prenom = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[Assert\Length(min: 2, max: 100,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères")]
    private ?string $nom = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide")]
    #[Assert\Length(max: 150, maxMessage: "L'email ne peut pas dépasser {{ limit }} caractères")]
    private ?string $email = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20, maxMessage: "Le téléphone ne peut pas dépasser {{ limit }} caractères")]
    private ?string $telephone = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le lieu est obligatoire")]
    private ?string $lieu = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "La profession est obligatoire")]
    private ?string $profession = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La motivation est obligatoire")]
    #[Assert\Length(min: 50, minMessage: "La motivation doit contenir au moins {{ limit }} caractères")]
    private ?string $motivation = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    private array $competences = [];

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    private array $domaines = [];

    #[ORM\Column]
    private ?int $disponibilite = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private bool $estActif = true;

    #[ORM\Column(length: 20)]
    private ?string $statut = 'en_attente';

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->estActif = true;
        $this->statut = 'en_attente';
        $this->competences = [];
        $this->domaines = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
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

    public function getNomComplet(): string
    {
        return $this->prenom . ' ' . $this->nom;
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

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(string $profession): static
    {
        $this->profession = $profession;

        return $this;
    }

    public function getMotivation(): ?string
    {
        return $this->motivation;
    }

    public function setMotivation(string $motivation): static
    {
        $this->motivation = $motivation;

        return $this;
    }

    public function getCompetences(): array
    {
        return $this->competences;
    }

    public function setCompetences(?array $competences): static
    {
        $this->competences = $competences ?? [];

        return $this;
    }

    public function addCompetence(string $competence): static
    {
        if (!in_array($competence, $this->competences, true)) {
            $this->competences[] = $competence;
        }

        return $this;
    }

    public function removeCompetence(string $competence): static
    {
        $key = array_search($competence, $this->competences, true);
        if ($key !== false) {
            unset($this->competences[$key]);
            $this->competences = array_values($this->competences);
        }

        return $this;
    }

    public function getDomaines(): array
    {
        return $this->domaines;
    }

    public function setDomaines(?array $domaines): static
    {
        $this->domaines = $domaines ?? [];

        return $this;
    }

    public function addDomaine(string $domaine): static
    {
        if (!in_array($domaine, $this->domaines, true)) {
            $this->domaines[] = $domaine;
        }

        return $this;
    }

    public function removeDomaine(string $domaine): static
    {
        $key = array_search($domaine, $this->domaines, true);
        if ($key !== false) {
            unset($this->domaines[$key]);
            $this->domaines = array_values($this->domaines);
        }

        return $this;
    }

    public function getDisponibilite(): ?int
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(int $disponibilite): static
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }

    public function getDisponibiliteText(): string
    {
        $texts = [
            1 => 'Quelques heures par semaine',
            2 => 'Un jour par semaine',
            3 => 'Plusieurs jours par semaine',
            4 => 'Temps plein occasionnel',
            5 => 'Disponible pour missions ponctuelles'
        ];
        
        return $texts[$this->disponibilite] ?? 'Non spécifié';
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

    public function isEstActif(): bool
    {
        return $this->estActif;
    }

    public function setEstActif(bool $estActif): static
    {
        $this->estActif = $estActif;

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

    public function getStatutText(): string
    {
        $texts = [
            'en_attente' => 'En attente',
            'en_revue' => 'En revue',
            'accepte' => 'Accepté',
            'refuse' => 'Refusé',
            'archive' => 'Archivé'
        ];
        
        return $texts[$this->statut] ?? $this->statut;
    }

    public function getCompetencesList(): string
    {
        return implode(', ', $this->competences);
    }

    public function getDomainesList(): string
    {
        return implode(', ', $this->domaines);
    }
}