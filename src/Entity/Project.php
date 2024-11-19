<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;

#[ApiResource(
    normalizationContext: ['groups' => ['project:read']],
    denormalizationContext: ['groups' => ['project:write']],
    operations: [
        new GetCollection(),
        new Post(security: "is_granted('ROLE_USER')"),
        new Get(),
        new Put(security: "is_granted('ROLE_USER') and object.getOwner() == user"),
        new Patch(security: "is_granted('ROLE_USER') and object.getOwner() == user"),
        new Delete(security: "is_granted('ROLE_USER') and object.getOwner() == user")
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'title' => 'partial',
    'summary' => 'partial',
    'user' => 'exact',
    'organization' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'title', 'downloadCount', 'likesCount'])]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['project:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[Groups(['project:read', 'project:write'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 255)]
    #[Groups(['project:read', 'project:write'])]
    private ?string $summary = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Groups(['project:read', 'project:write'])]
    private ?string $longDescription = null;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['project:read', 'project:write'])]
    private array $gallery = [];

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['project:read', 'project:write'])]
    private array $changelog = [];

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['project:read', 'project:write'])]
    private array $versions = [];

    #[ORM\Column]
    #[Groups(['project:read', 'project:write'])]
    private bool $isDownloadable = false;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['project:read', 'project:write'])]
    private ?string $downloadUrl = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['project:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['project:read', 'project:write'])]
    private ?Organization $organization = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'likedProjects')]
    #[ORM\JoinTable(name: 'project_likes')]
    #[Groups(['project:read'])]
    private Collection $likes;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'favoritedProjects')]
    #[ORM\JoinTable(name: 'project_favorites')]
    #[Groups(['project:read'])]
    private Collection $favorites;

    #[ORM\Column]
    #[Groups(['project:read'])]
    private int $downloadCount = 0;

    #[ORM\Column]
    #[Groups(['project:read'])]
    private int $likesCount = 0;

    #[ORM\Column]
    #[Groups(['project:read'])]
    private int $favoritesCount = 0;

    #[ORM\Column]
    #[Groups(['project:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Groups(['project:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->favorites = new ArrayCollection();
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): static
    {
        $this->summary = $summary;
        return $this;
    }

    public function getLongDescription(): ?string
    {
        return $this->longDescription;
    }

    public function setLongDescription(string $longDescription): static
    {
        $this->longDescription = $longDescription;
        return $this;
    }

    public function getGallery(): array
    {
        return $this->gallery;
    }

    public function setGallery(array $gallery): static
    {
        $this->gallery = $gallery;
        return $this;
    }

    public function getChangelog(): array
    {
        return $this->changelog;
    }

    public function setChangelog(array $changelog): static
    {
        $this->changelog = $changelog;
        return $this;
    }

    public function getVersions(): array
    {
        return $this->versions;
    }

    public function setVersions(array $versions): static
    {
        $this->versions = $versions;
        return $this;
    }

    public function isDownloadable(): bool
    {
        return $this->isDownloadable;
    }

    public function setIsDownloadable(bool $isDownloadable): static
    {
        $this->isDownloadable = $isDownloadable;
        return $this;
    }

    public function getDownloadUrl(): ?string
    {
        return $this->downloadUrl;
    }

    public function setDownloadUrl(?string $downloadUrl): static
    {
        $this->downloadUrl = $downloadUrl;
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

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): static
    {
        $this->organization = $organization;
        return $this;
    }

    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(User $user): static
    {
        if (!$this->likes->contains($user)) {
            $this->likes->add($user);
            $this->likesCount++;
        }
        return $this;
    }

    public function removeLike(User $user): static
    {
        if ($this->likes->removeElement($user)) {
            $this->likesCount--;
        }
        return $this;
    }

    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(User $user): static
    {
        if (!$this->favorites->contains($user)) {
            $this->favorites->add($user);
            $this->favoritesCount++;
        }
        return $this;
    }

    public function removeFavorite(User $user): static
    {
        if ($this->favorites->removeElement($user)) {
            $this->favoritesCount--;
        }
        return $this;
    }

    public function getDownloadCount(): int
    {
        return $this->downloadCount;
    }

    public function incrementDownloadCount(): static
    {
        $this->downloadCount++;
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
}