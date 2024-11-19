<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\MeController;

#[ApiResource(
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']],
    operations: [
        new Get(
            name: 'me',
            uriTemplate: '/me',
            controller: MeController::class,
            read: false,
            security: "is_granted('ROLE_USER')"
        ),
        new Get(security: "is_granted('ROLE_USER')"),
        new Put(
            security: "is_granted('ROLE_USER') and object == user",
            securityMessage: 'You can only edit your own profile.'
        ),
        new GetCollection(security: "is_granted('ROLE_USER')")
    ],
    security: "is_granted('ROLE_USER')"
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_UUID', fields: ['email'])]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['user:read'])]
    private ?string $uuid = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email]
    #[Assert\NotBlank]
    #[Groups(['user:read'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private array $roles = [];

    #[ORM\Column(nullable: true)]
    #[ApiProperty(readable: false)]
    #[Assert\NotCompromisedPassword]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read'])]
    private ?string $oAuthId = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read'])]
    private ?string $oAuthProvider = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $displayName = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $bio = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $avatarUrl = null;

    #[ORM\OneToMany(targetEntity: Organization::class, mappedBy: 'owner')]
    #[Groups(['user:read'])]
    private Collection $ownedOrganizations;

    #[ORM\ManyToMany(targetEntity: Organization::class, mappedBy: 'members')]
    #[Groups(['user:read'])]
    private Collection $memberOrganizations;

    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'user')]
    #[Groups(['user:read'])]
    private Collection $projects;

    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'likes')]
    #[Groups(['user:read'])]
    private Collection $likedProjects;

    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'favorites')]
    #[Groups(['user:read'])]
    private Collection $favoritedProjects;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->ownedOrganizations = new ArrayCollection();
        $this->memberOrganizations = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->likedProjects = new ArrayCollection();
        $this->favoritedProjects = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function updateTimestamp(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;
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

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getOAuthId(): ?string
    {
        return $this->oAuthId;
    }

    public function setOAuthId(?string $oAuthId): static
    {
        $this->oAuthId = $oAuthId;
        return $this;
    }

    public function getOAuthProvider(): ?string
    {
        return $this->oAuthProvider;
    }

    public function setOAuthProvider(?string $oAuthProvider): static
    {
        $this->oAuthProvider = $oAuthProvider;
        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): static
    {
        $this->displayName = $displayName;
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;
        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): static
    {
        $this->avatarUrl = $avatarUrl;
        return $this;
    }

    public function getOwnedOrganizations(): Collection
    {
        return $this->ownedOrganizations;
    }

    public function getMemberOrganizations(): Collection
    {
        return $this->memberOrganizations;
    }

    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function getLikedProjects(): Collection
    {
        return $this->likedProjects;
    }

    public function getFavoritedProjects(): Collection
    {
        return $this->favoritedProjects;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}