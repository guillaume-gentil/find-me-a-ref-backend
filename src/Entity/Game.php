<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({
     * "games_collection",
     * "game_item"
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * @Groups({
     * "games_collection",
     * "game_item"
     * })
     */
    private $date;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Assert\NotBlank
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Arena::class, inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="the ID of arena is not correct.")
     * @Groups({
     * "games_collection",
     * "game_item"
     * })
     */
    private $arena;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="the ID of type is not correct.")
     * @Groups({
     * "games_collection",
     * "game_item"
     * })
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity=Team::class, inversedBy="games")
     * @Groups({
     * "games_collection",
     * "game_item"
     * })
     */
    private $teams;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="games")
     * @Groups({
     * "games_collection",
     * "game_item"
     * })
     */
    private $users;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getArena(): ?Arena
    {
        return $this->arena;
    }

    public function setArena(?Arena $arena): self
    {
        $this->arena = $arena;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        $this->teams->removeElement($team);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);
        
        // refresh indexes of users (store in ArrayCollection)
        // fix response format issue (collection vs array) 
        // source : https://stackoverflow.com/questions/35250599/reindexing-arraycollection-elements-in-restapi
        $this->users = new ArrayCollection($this->users->getValues());

        return $this;
    }
}
