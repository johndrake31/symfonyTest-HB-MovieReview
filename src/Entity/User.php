<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * 
     * @Assert\EqualTo(propertyPath="password")
     */
    private $passwordConfirm;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity=Movie::class, mappedBy="user", orphanRemoval=true)
     */
    private $movies;

    /**
     * @ORM\OneToMany(targetEntity=Impressions::class, mappedBy="user", orphanRemoval=true)
     */
    private $impressions;

    public function __construct()
    {
        $this->movies = new ArrayCollection();
        $this->impressions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPasswordConfirm(): ?string
    {
        return $this->passwordConfirm;
    }

    public function setPasswordConfirm(string $passwordConfirm): self
    {
        $this->passwordConfirm = $passwordConfirm;

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

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
    }
    public function getRoles()
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }


    public function getUserIdentifier()
    {
        return $this->username;
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection|Movie[]
     */
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    public function addMovie(Movie $movie): self
    {
        if (!$this->movies->contains($movie)) {
            $this->movies[] = $movie;
            $movie->setUser($this);
        }

        return $this;
    }

    public function removeMovie(Movie $movie): self
    {
        if ($this->movies->removeElement($movie)) {
            // set the owning side to null (unless already changed)
            if ($movie->getUser() === $this) {
                $movie->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Impressions[]
     */
    public function getImpressions(): Collection
    {
        return $this->impressions;
    }

    public function addImpression(Impressions $impression): self
    {
        if (!$this->impressions->contains($impression)) {
            $this->impressions[] = $impression;
            $impression->setUser($this);
        }

        return $this;
    }

    public function removeImpression(Impressions $impression): self
    {
        if ($this->impressions->removeElement($impression)) {
            // set the owning side to null (unless already changed)
            if ($impression->getUser() === $this) {
                $impression->setUser(null);
            }
        }

        return $this;
    }
}
