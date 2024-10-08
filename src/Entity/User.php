<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
// Permet de mettre en place une contrainte qui évite d'avoir en doublon une adresse email et un pseudo.
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cet email.')]
#[UniqueEntity(fields: ['nickname'], message: 'Il existe déjà un compte avec ce pseudonyme.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // ***********************
    // Roles
    // ***********************

    /**
     * @var list<string>
     */
    #[ORM\Column]
    private array $roles = [];

    // ***********************
    // Pseudo
    // ***********************
    #[Assert\NotBlank(message: "Le pseudonyme est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le pseudonyme ne doit pas dépasser {{ limit }} caractères.',
    )]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $nickname = null;

    // ***********************
    // Prénom
    // ***********************

    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le prénom ne doit pas dépasser {{ limit }} caractères.',
    )]
    #[Assert\Regex(
        pattern: "/^[0-9a-zA-Z-_' áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]+$/i",
        match: true,
        message: 'Seuls les lettres, les chiffres, l\'undescore et le tiret sont autorisés pour le prénom.',
    )]
    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    // ***********************
    // Nom
    // ***********************

    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom ne doit pas dépasser {{ limit }} caractères.',
    )]
    #[Assert\Regex(
        pattern: "/^[0-9a-zA-Z-_' áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]+$/i",
        match: true,
        message: 'Seuls les lettres, les chiffres, l\'undescore et le tiret sont autorisés pour le nom.',
    )]
    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    // ***********************
    // Email
    // ***********************

    #[Assert\NotBlank(message: "L'adresse email est obligatoire.")]
    #[Assert\Length(
        max: 180,
        maxMessage: 'L\'adresse email ne doit pas dépasser {{ limit }} caractères.',
    )]
    #[Assert\Email(
        message: 'Le format de l\'adresse email {{ value }} ne correspond pas au bon format.',
    )]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[Assert\NotBlank(message: "La date de naissance est obligatoire.")]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $birth = null;

    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le numéro de téléphone ne peut pas contenir plus de {{ limit }} caractères',
    )]
    #[Assert\Regex(
        "/^[0-9\s\-\(\)\+]{6,60}$/",
        message: 'Le numéro de téléphone est incorrect.',
    )]
    #[ORM\Column(length: 255)]
    private ?string $phone = null;
    // ***********************
    // Mot de passe
    // ***********************

    /**
     * @var string The hashed password
     */
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire.")]
    #[Assert\Length(
        min: 12,
        max: 255,
        minMessage: 'Le mot de passe doit contenir au minimum {{ limit }} caractères.',
        maxMessage: 'Le mot de passe doit contenir au maximum {{ limit }} caractères.',
    )]
    #[Assert\Regex(
        pattern: "/^(?=.*[a-zà-ÿ])(?=.*[A-ZÀ-Ỳ])(?=.*[0-9])(?=.*[^a-zà-ÿA-ZÀ-Ỳ0-9]).{11,255}$/",
        match: true,
        message: "Le mot de passe doit contentir au moins une lettre miniscule, majuscule, un chiffre et un caractère spécial.",
    )]
    #[ORM\Column]
    private ?string $password = null;

    // ***********************
    // Date de création
    // ***********************

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    // ***********************
    // Date de modification
    // ***********************

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    // ***********************
    // Vérification de l'adresse email
    // ***********************

    #[ORM\Column]
    private bool $isVerified = false;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $projects;

    /**
     * @var Collection<int, ProjectUser>
     */
    #[ORM\OneToMany(targetEntity: ProjectUser::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $projectUsers;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $comments;

    /**
     * @var Collection<int, Reporting>
     */
    #[ORM\OneToMany(targetEntity: Reporting::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $reportings;

    #[ORM\Column]
    private ?bool $isBanned = false;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $bannedAt = null;

    /**
     * @var Collection<int, Contact>
     */
    #[ORM\OneToMany(targetEntity: Contact::class, mappedBy: 'user')]
    private Collection $contacts;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->projectUsers = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->reportings = new ArrayCollection();
        $this->contacts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setUser($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getUser() === $this) {
                $project->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProjectUser>
     */
    public function getProjectUsers(): Collection
    {
        return $this->projectUsers;
    }

    public function addProjectUser(ProjectUser $projectUser): static
    {
        if (!$this->projectUsers->contains($projectUser)) {
            $this->projectUsers->add($projectUser);
            $projectUser->setUser($this);
        }

        return $this;
    }

    public function removeProjectUser(ProjectUser $projectUser): static
    {
        if ($this->projectUsers->removeElement($projectUser)) {
            // set the owning side to null (unless already changed)
            if ($projectUser->getUser() === $this) {
                $projectUser->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): static
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * @return Collection<int, Reporting>
     */
    public function getReportings(): Collection
    {
        return $this->reportings;
    }

    public function addReporting(Reporting $reporting): static
    {
        if (!$this->reportings->contains($reporting)) {
            $this->reportings->add($reporting);
            $reporting->setUser($this);
        }

        return $this;
    }

    public function removeReporting(Reporting $reporting): static
    {
        if ($this->reportings->removeElement($reporting)) {
            // set the owning side to null (unless already changed)
            if ($reporting->getUser() === $this) {
                $reporting->setUser(null);
            }
        }

        return $this;
    }

    public function isBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setBanned(bool $isBanned): static
    {
        $this->isBanned = $isBanned;

        return $this;
    }

    public function getBannedAt(): ?\DateTimeImmutable
    {
        return $this->bannedAt;
    }

    public function setBannedAt(?\DateTimeImmutable $bannedAt): static
    {
        $this->bannedAt = $bannedAt;

        return $this;
    }

    public function getBirth(): ?\DateTimeImmutable
    {
        return $this->birth;
    }

    public function setBirth(?\DateTimeImmutable $birth): static
    {
        $this->birth = $birth;

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

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): static
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setUser($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): static
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getUser() === $this) {
                $contact->setUser(null);
            }
        }

        return $this;
    }
}