<?php

declare(strict_types=1);

namespace App\Entity\User;

use App\Repository\User\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'])]
#[UniqueEntity(fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, unique: true, nullable: false)]
    #[Assert\NotBlank(message: 'Privalomas laukas')]
    #[Assert\NotNull]
    #[Assert\Length(
        min: 3,
        max: 20,
        minMessage: 'Vartotojo vardas turėtų būti ne trumpesnis nei 3 simboliai',
        maxMessage: 'Vartotojo vardas turėtų būti ne ilgesnis nei 20 simbolių'
    )]
    #[Assert\Regex('/^[a-zA-Z0-9_-]*$/', message: 'Negalima naudoti neleistinų simbolių')]
    private string $username;

    #[ORM\Column(length: 180, unique: true, nullable: false)]
    #[Assert\NotBlank(message: 'Privalomas laukas')]
    #[Assert\NotNull]
    #[Assert\Email(message: 'El. pašto adresas privalo būti validus')]
    private string $email;

    #[ORM\Column(nullable: false)]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(nullable: false)]
    #[Assert\NotBlank(message: 'Privalomas laukas')]
    #[Assert\NotNull]
    #[Assert\Length(
        min: 5,
        minMessage: 'Slaptažodis turėtų būti ne trumpesnis nei 5 simboliai',
    )]
    private string $password;

    #[ORM\Column(nullable: false)]
    #[Assert\NotNull()]
    #[Assert\IsTrue(message: 'Privalomas laukas')]
    private bool $terms;

    #[ORM\Column(nullable: false)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(options: ["default" => false])]
    private bool $activated = false;

    #[ORM\OneToOne(mappedBy: 'owner', targetEntity: UserProfile::class)]
    private UserProfile $userProfile;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: UserPicture::class)]
    private Collection $userPictures;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
        $this->userPictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
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

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isActivated(): ?bool
    {
        return $this->activated;
    }

    public function setActivated(bool $activated): self
    {
        $this->activated = $activated;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isTerms(): bool
    {
        return $this->terms;
    }

    public function setTerms(bool $terms): void
    {
        $this->terms = $terms;
    }

    public function getUserProfile(): UserProfile
    {
        return $this->userProfile;
    }

    public function hasUserProfile(): bool
    {
        return isset($this->userProfile);
    }

    public function getUserPictures(): ?Collection
    {
        return $this->userPictures;
    }
}
