<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[Vich\Uploadable]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['project_read'])]
    private ?int $id = null;

    #############################
    ### title
    #############################

    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le titre du projet ne peut pas contenir plus de {{ limit }} caractères',
    )]
    #[ORM\Column(length: 255)]
    #[Groups(['project_read', 'project_write'])]
    private ?string $title = null;

    #############################
    ### slug
    #############################
    #[Gedmo\Slug(fields: ['title'])]
    #[ORM\Column(length: 255)]
    #[Groups(['project_read', 'project_write'])]
    private ?string $slug = null;

    #############################
    ### image
    #############################

    #[Assert\File(
        maxSize: '5120k',
        extensions: ['png', 'jpg', 'jpeg', 'webp'],
        extensionsMessage: 'Seuls les images en formats .png, .jpg, .jpeg, où .webp sont autorisés',
    )]
    #[Vich\UploadableField(mapping: 'projects', fileNameProperty: 'image')]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    #[Groups(['project_read', 'project_write'])]
    private ?string $image = null;

    #############################
    ### content
    #############################

    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(
        min: 50,
        max: 500,
        minMessage: 'La description du projet doit contenir au moins {{ limit }} caractères',
        maxMessage: 'La description du projet ne peut pas contenir plus de {{ limit }} caractères',
    )]
    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['project_read', 'project_write'])]
    private ?string $description = null;

    #############################
    ### content
    #############################

    #[Assert\NotBlank(message: "Le contenu est obligatoire.")]
    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['project_read', 'project_write'])]
    private ?string $content = null;

    #############################
    ### address
    #############################

    #[ORM\Column(length: 255)]
    #[Groups(['project_read', 'project_write'])]
    private ?string $address = null;

    #############################
    ### street
    #############################

    #[Assert\Length(
        max: 255,
        maxMessage: 'La rue ne doit pas dépasser {{ limit }} caractères.',
    )]
    #[ORM\Column(length: 255)]
    #[Groups(['project_read', 'project_write'])]
    private ?string $street = null;

    #############################
    ### code
    #############################

    #[Assert\NotBlank(message: "Le code postale est obligatoire.")]
    #[Assert\PositiveOrZero(message: "Le code postale ne peu pas être inférieur a zéro.")]
    #[ORM\Column]
    #[Groups(['project_read', 'project_write'])]
    private ?int $code = null;

    #############################
    ### city
    #############################

    #[Assert\NotBlank(message: "La ville est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: 'La ville ne doit pas dépasser {{ limit }} caractères.',
    )]
    #[ORM\Column(length: 255)]
    #[Groups(['project_read', 'project_write'])]
    private ?string $city = null;

    #############################
    ### longitude
    #############################

    #[ORM\Column]
    #[Groups(['project_read', 'project_write'])]
    private ?float $longitude = null;

    #############################
    ### latitude
    #############################

    #[ORM\Column]
    #[Groups(['project_read', 'project_write'])]
    private ?float $latitude = null;

    #############################
    ### phone
    #############################

    #[Assert\Length(
        max: 255,
        maxMessage: 'Le numéro de téléphone ne peut pas contenir plus de {{ limit }} caractères',
    )]
    #[Assert\Regex(
        "/^[0-9\s\-\(\)\+]{6,60}$/",
        message: 'Le numéro de téléphone est incorrect.',
    )]
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['project_read', 'project_write'])]
    private ?string $phone = null;

    #############################
    ### email
    #############################

    #[Assert\NotBlank(message: "L'adresse email est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: 'L\'adresse email ne doit pas dépasser {{ limit }} caractères.',
    )]
    #[Assert\Email(
        message: 'Le format de l\'adresse email {{ value }} ne correspond pas au bon format.',
    )]
    #[ORM\Column(length: 255)]
    #[Groups(['project_read', 'project_write'])]
    private ?string $email = null;

    #############################
    ### availableSpace
    #############################

    #[Assert\PositiveOrZero(message: "Le nombre de participants maximum ne peu pas être inférieur a zéro.")]
    #[ORM\Column(nullable: true)]
    #[Groups(['project_read', 'project_write'])]
    private ?int $availableSpace = null;

    #############################
    ### start date
    #############################

    #[Assert\NotBlank(message: "La date de début est obligatoire.")]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Groups(['project_read', 'project_write'])]
    private ?\DateTimeImmutable $startDate = null;

    #############################
    ### end date
    #############################

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Groups(['project_read', 'project_write'])]
    private ?\DateTimeImmutable $endDate = null;

    #############################
    ### created at
    #############################

    #[ORM\Column(nullable: true)]
    #[Groups(['project_read', 'project_write'])]
    private ?\DateTimeImmutable $createdAt = null;

    #############################
    ### updated at
    #############################

    #[ORM\Column(nullable: true)]
    #[Groups(['project_read', 'project_write'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['project_read', 'project_write'])]
    private ?User $user = null;

    #[Assert\NotBlank(
        message: "Le choix d'une catégorie est obligatoire."
    )]
    #[Assert\Type(
        type: Category::class,
        message: 'Cette catégorie est invalide.',
    )]
    #[Groups(['project_read', 'project_write'])]
    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'projects')]
    private Collection $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
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

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(?int $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getAvailableSpace(): ?int
    {
        return $this->availableSpace;
    }

    public function setAvailableSpace(?int $availableSpace): static
    {
        $this->availableSpace = $availableSpace;

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

}
