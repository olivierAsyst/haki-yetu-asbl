<?php

namespace App\Entity;

use App\Repository\RelationMediaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RelationMediaRepository::class)]
class RelationMedia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre de l'article média est obligatoire.")]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du média est obligatoire.")]
    private ?string $nomDeMedia = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datePublication = null;

    #[ORM\Column(length: 500)]
    #[Assert\NotBlank(message: "Le lien vers l'article est obligatoire.")]
    #[Assert\Url(message: "Le lien doit être une URL valide.")]
    private ?string $lien = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $logoMedia = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $typeMedia = null;

    #[ORM\Column]
    private bool $actif = true;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
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

    public function getNomDeMedia(): ?string
    {
        return $this->nomDeMedia;
    }

    public function setNomDeMedia(string $nomDeMedia): static
    {
        $this->nomDeMedia = $nomDeMedia;

        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->datePublication;
    }

    public function setDatePublication(?\DateTimeInterface $datePublication): static
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(string $lien): static
    {
        $this->lien = $lien;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLogoMedia(): ?string
    {
        return $this->logoMedia;
    }

    public function setLogoMedia(?string $logoMedia): static
    {
        $this->logoMedia = $logoMedia;

        return $this;
    }

    public function getTypeMedia(): ?string
    {
        return $this->typeMedia;
    }

    public function setTypeMedia(?string $typeMedia): static
    {
        $this->typeMedia = $typeMedia;

        return $this;
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

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function __toString(): string
    {
        return $this->titre ?? '';
    }

    public function getDomainFromLien(): ?string
    {
        if (!$this->lien) {
            return null;
        }

        $urlParts = parse_url($this->lien);
        return $urlParts['host'] ?? null;
    }

    public function getShortDescription(int $length = 150): string
    {
        if (!$this->description) {
            return '';
        }

        if (mb_strlen($this->description) <= $length) {
            return $this->description;
        }

        return rtrim(mb_substr($this->description, 0, $length)) . '…';
    }
}