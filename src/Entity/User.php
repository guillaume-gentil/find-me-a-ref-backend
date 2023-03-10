<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const ROLES = ["ROLE_ADMIN", "ROLE_TEAMHEAD", "ROLE_REFEREE", "ROLE_TEMPORARY"];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({
     * "games_collection",
     * "users_collection"
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({
     * "games_collection",
     * "users_collection"
     * })
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({
     * "games_collection",
     * "users_collection"
     * })
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank
     * @Assert\Email
     * @Groups({
     * "games_collection",
     * "users_collection"
     * })
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Assert\Choice(choices=User::ROLES, multiple=true)
     * @Groups({"users_collection"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Regex(
     *  pattern="/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/i",
     *  message="Merci de renseigner au moins 8 caractères, dont une lettre, un chiffre et un caractère spécial.",
     *  groups={"users_new_password"}
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="integer", nullable=true, unique=true)
     * @Groups({
     * "games_collection",
     * "users_collection"
     * })
     */
    private $licenceId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @groups({
     * "games_collection",
     * "users_collection"
     * })
     */
    private $level;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"users_collection"})
     */
    private $address;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"users_collection"})
     */
    private $zipCode;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({
     * "games_collection",
     * "users_collection"
     * })
     */
    private $longitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({
     * "games_collection",
     * "users_collection"
     * })
     */
    private $latitude;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity=Game::class, mappedBy="users")
     */
    private $games;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({
     * "games_collection",
     * "users_collection"
     * })
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $signUpToken;

    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        // $roles[] = 'ROLE_USER';  // not necessary => prefere to use inheritance

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

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLicenceId(): ?int
    {
        return $this->licenceId;
    }

    public function setLicenceId(?int $licenceId): self
    {
        $this->licenceId = $licenceId;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(?string $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): ?int
    {
        return $this->zipCode;
    }

    public function setZipCode(?int $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->addUser($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->removeElement($game)) {
            $game->removeUser($this);
        }

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getSignUpToken(): ?string
    {
        return $this->signUpToken;
    }

    public function setSignUpToken(?string $signUpToken): self
    {
        $this->signUpToken = $signUpToken;

        return $this;
    }
}