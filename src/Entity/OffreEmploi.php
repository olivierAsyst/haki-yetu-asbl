<?php

namespace App\Entity;

use App\Repository\OffreEmploiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: OffreEmploiRepository::class)]
#[Vich\Uploadable]
class OffreEmploi
{
    public const TYPE_CDI = 'cdi';
    public const TYPE_CDD = 'cdd';
    public const TYPE_STAGE = 'stage';
    public const TYPE_VOLONTARIAT = 'volontariat';

    public const TYPES = [
        'CDI' => self::TYPE_CDI,
        'CDD' => self::TYPE_CDD,
        'Stage' => self::TYPE_STAGE,
        'Volontariat' => self::TYPE_VOLONTARIAT,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(max: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le lieu est obligatoire.')]
    private ?string $lieu = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: [self::TYPE_CDI, self::TYPE_CDD, self::TYPE_STAGE, self::TYPE_VOLONTARIAT])]
    private string $type = self::TYPE_CDI;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $datePublication = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateExpiration = null;

    #[ORM\Column(nullable: true)]
    private ?int $salaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $salaireInfo = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $fileDeadline = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fichierOffre = null;

    #[ORM\Column(nullable: true)]
    private ?int $fichierOffreSize = null;

    #[Vich\UploadableField(mapping: 'offre_emploi_files', fileNameProperty: 'fichierOffre', size: 'fichierOffreSize')]
    #[Assert\File(maxSize: '10M', mimeTypes: ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'], mimeTypesMessage: 'Veuillez déposer un fichier PDF ou Word (10 Mo max).')]
    private ?\Symfony\Component\HttpFoundation\File\File $fichierOffreFile = null;

    #[ORM\Column]
    private bool $actif = true;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'offreEmploi', targetEntity: Candidature::class)]
    private $candidatures;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->datePublication = new \DateTimeImmutable();
        $this->candidatures = new \Doctrine\Common\Collections\ArrayCollection();
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

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;
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
        return array_flip(self::TYPES)[$this->type] ?? 'CDI';
    }

    public function getDatePublication(): ?\DateTimeImmutable
    {
        return $this->datePublication;
    }

    public function setDatePublication(?\DateTimeImmutable $datePublication): static
    {
        $this->datePublication = $datePublication;
        return $this;
    }

    public function getDateExpiration(): ?\DateTimeImmutable
    {
        return $this->dateExpiration;
    }

    public function setDateExpiration(?\DateTimeImmutable $dateExpiration): static
    {
        $this->dateExpiration = $dateExpiration;
        return $this;
    }

    public function isExpired(): bool
    {
        return $this->dateExpiration !== null && $this->dateExpiration < new \DateTimeImmutable();
    }

    public function isFileExpired(): bool
    {
        return $this->fileDeadline !== null && $this->fileDeadline < new \DateTimeImmutable();
    }

    public function isAvailable(): bool
    {
        if (!$this->actif) {
            return false;
        }
        if ($this->isExpired()) {
            return false;
        }
        if ($this->isFileExpired()) {
            return false;
        }
        return true;
    }

    public function getAvailabilityStatus(): string
    {
        if (!$this->actif) {
            return 'désactivée';
        }
        if ($this->isFileExpired()) {
            return 'date limite dépassée';
        }
        if ($this->isExpired()) {
            return 'expirée';
        }
        return 'disponible';
    }

    public function getSalaire(): ?int
    {
        return $this->salaire;
    }

    public function setSalaire(?int $salaire): static
    {
        $this->salaire = $salaire;
        return $this;
    }

    public function getSalaireInfo(): ?string
    {
        return $this->salaireInfo;
    }

    public function setSalaireInfo(?string $salaireInfo): static
    {
        $this->salaireInfo = $salaireInfo;
        return $this;
    }

    public function getFileDeadline(): ?\DateTimeImmutable
    {
        return $this->fileDeadline;
    }

    public function setFileDeadline(?\DateTimeImmutable $fileDeadline): static
    {
        $this->fileDeadline = $fileDeadline;
        return $this;
    }

    public function getFichierOffre(): ?string
    {
        return $this->fichierOffre;
    }

    public function setFichierOffre(?string $fichierOffre): static
    {
        $this->fichierOffre = $fichierOffre;
        return $this;
    }

    public function getFichierOffreSize(): ?int
    {
        return $this->fichierOffreSize;
    }

    public function setFichierOffreSize(?int $fichierOffreSize): static
    {
        $this->fichierOffreSize = $fichierOffreSize;
        return $this;
    }

    public function setFichierOffreFile(?\Symfony\Component\HttpFoundation\File\File $fichierOffreFile = null): static
    {
        $this->fichierOffreFile = $fichierOffreFile;

        if (null !== $fichierOffreFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getFichierOffreFile(): ?\Symfony\Component\HttpFoundation\File\File
    {
        return $this->fichierOffreFile;
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    public function __toString(): string
    {
        return $this->titre ?? '';
    }

    public function getCandidatures(): Collection
    {
        return $this->candidatures;
    }

    public function addCandidature(Candidature $candidature): static
    {
        if (!$this->candidatures->contains($candidature)) {
            $this->candidatures->add($candidature);
            $candidature->setOffreEmploi($this);
        }

        return $this;
    }

    public function removeCandidature(Candidature $candidature): static
    {
        if ($this->candidatures->removeElement($candidature)) {
            if ($candidature->getOffreEmploi() === $this) {
                $candidature->setOffreEmploi(null);
            }
        }

        return $this;
    }
}