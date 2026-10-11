<?php

namespace App\Entity;

use App\Repository\CandidatureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: CandidatureRepository::class)]
#[Vich\Uploadable]
class Candidature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(max: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire.')]
    #[Assert\Length(max: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire.')]
    #[Assert\Email(message: 'L\'email n\'est pas valide.')]
    #[Assert\Length(max: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'Le téléphone est obligatoire.')]
    #[Assert\Length(max: 20)]
    private ?string $telephone = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cvFile = null;

    #[ORM\Column(nullable: true)]
    private ?int $cvFileSize = null;

    #[Vich\UploadableField(mapping: 'candidature_cv', fileNameProperty: 'cvFile', size: 'cvFileSize')]
    #[Assert\File(maxSize: '5M', mimeTypes: ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'], mimeTypesMessage: 'Veuillez déposer un fichier PDF ou Word (5 Mo max).')]
    private ?\Symfony\Component\HttpFoundation\File\File $cvFileFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lettreMotivationFile = null;

    #[ORM\Column(nullable: true)]
    private ?int $lettreMotivationFileSize = null;

    #[Vich\UploadableField(mapping: 'candidature_lettre', fileNameProperty: 'lettreMotivationFile', size: 'lettreMotivationFileSize')]
    #[Assert\File(maxSize: '5M', mimeTypes: ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'], mimeTypesMessage: 'Veuillez déposer un fichier PDF ou Word (5 Mo max).')]
    private ?\Symfony\Component\HttpFoundation\File\File $lettreMotivationFileFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $autresDocuments = null;

    #[ORM\Column(nullable: true)]
    private ?int $autresDocumentsSize = null;

    #[Vich\UploadableField(mapping: 'candidature_autres', fileNameProperty: 'autresDocuments', size: 'autresDocumentsSize')]
    #[Assert\File(maxSize: '10M', mimeTypes: ['application/pdf', 'application/zip', 'application/x-zip-compressed'], mimeTypesMessage: 'Veuillez déposer un fichier PDF ou ZIP (10 Mo max).')]
    private ?\Symfony\Component\HttpFoundation\File\File $autresDocumentsFile = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: OffreEmploi::class, inversedBy: 'candidatures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?OffreEmploi $offreEmploi = null;

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
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

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;
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

    public function getCvFile(): ?string
    {
        return $this->cvFile;
    }

    public function setCvFile(?string $cvFile): static
    {
        $this->cvFile = $cvFile;
        return $this;
    }

    public function getCvFileSize(): ?int
    {
        return $this->cvFileSize;
    }

    public function setCvFileSize(?int $cvFileSize): static
    {
        $this->cvFileSize = $cvFileSize;
        return $this;
    }

    public function setCvFileFile(?\Symfony\Component\HttpFoundation\File\File $cvFileFile = null): static
    {
        $this->cvFileFile = $cvFileFile;
        return $this;
    }

    public function getCvFileFile(): ?\Symfony\Component\HttpFoundation\File\File
    {
        return $this->cvFileFile;
    }

    public function getLettreMotivationFile(): ?string
    {
        return $this->lettreMotivationFile;
    }

    public function setLettreMotivationFile(?string $lettreMotivationFile): static
    {
        $this->lettreMotivationFile = $lettreMotivationFile;
        return $this;
    }

    public function getLettreMotivationFileSize(): ?int
    {
        return $this->lettreMotivationFileSize;
    }

    public function setLettreMotivationFileSize(?int $lettreMotivationFileSize): static
    {
        $this->lettreMotivationFileSize = $lettreMotivationFileSize;
        return $this;
    }

    public function setLettreMotivationFileFile(?\Symfony\Component\HttpFoundation\File\File $lettreMotivationFileFile = null): static
    {
        $this->lettreMotivationFileFile = $lettreMotivationFileFile;
        return $this;
    }

    public function getLettreMotivationFileFile(): ?\Symfony\Component\HttpFoundation\File\File
    {
        return $this->lettreMotivationFileFile;
    }

    public function getAutresDocuments(): ?string
    {
        return $this->autresDocuments;
    }

    public function setAutresDocuments(?string $autresDocuments): static
    {
        $this->autresDocuments = $autresDocuments;
        return $this;
    }

    public function getAutresDocumentsSize(): ?int
    {
        return $this->autresDocumentsSize;
    }

    public function setAutresDocumentsSize(?int $autresDocumentsSize): static
    {
        $this->autresDocumentsSize = $autresDocumentsSize;
        return $this;
    }

    public function setAutresDocumentsFile(?\Symfony\Component\HttpFoundation\File\File $autresDocumentsFile = null): static
    {
        $this->autresDocumentsFile = $autresDocumentsFile;
        return $this;
    }

    public function getAutresDocumentsFile(): ?\Symfony\Component\HttpFoundation\File\File
    {
        return $this->autresDocumentsFile;
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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getOffreEmploi(): ?OffreEmploi
    {
        return $this->offreEmploi;
    }

    public function setOffreEmploi(?OffreEmploi $offreEmploi): static
    {
        $this->offreEmploi = $offreEmploi;
        return $this;
    }

    public function __toString(): string
    {
        return $this->nom . ' ' . $this->prenom;
    }
}