<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;

#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_USER')",
            normalizationContext: ['groups' => ['organization:read']]
        ),
        new Post(
            security: "is_granted('ROLE_USER')",
            denormalizationContext: ['groups' => ['organization:create']],
            normalizationContext: ['groups' => ['organization:read']]
        ),
        new Get(
            security: "is_granted('ROLE_USER')",
            normalizationContext: ['groups' => ['organization:read', 'organization:detail']]
        ),
        new Put(
            security: "is_granted('ROLE_USER') and object.getOwner() == user",
            denormalizationContext: ['groups' => ['organization:update']],
            normalizationContext: ['groups' => ['organization:read']]
        ),
        new Delete(
            security: "is_granted('ROLE_USER') and object.getOwner() == user"
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial', 'url' => 'exact'])]
#[ApiFilter(OrderFilter::class, properties: ['name', 'createdAt'])]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Organization
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['organization:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[Groups(['organization:read', 'organization:create', 'organization:update'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[a-z0-9-]+$/', message: 'URL must contain only lowercase letters, numbers, and hyphens')]
    #[Groups(['organization:read', 'organization:create'])]
    private ?string $url = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['organization:read', 'organization:create', 'organization:update'])]
    private ?string $summary = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['organization:read'])]
    private ?User $owner = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'organization_members')]
    #[Groups(['organization:read', 'organization:detail'])]
    private Collection $members;

    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'organization')]
    #[Groups(['organization:read', 'organization:detail'])]
    private Collection $projects;

    #[ORM\Column]
    #[Groups(['organization:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Groups(['organization:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function updateTimestamp()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;
        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }
        return $this;
    }

    public function removeMember(User $member): self
    {
        $this->members->removeElement($member);
        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
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