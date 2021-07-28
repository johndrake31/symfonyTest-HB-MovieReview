<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MovieRepository::class)
 */
class Movie
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
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $summary;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $director;

    /**
     * @ORM\Column(type="datetime")
     */
    private $releaseyear;

    /**
     * @ORM\OneToMany(targetEntity=Impressions::class, mappedBy="movie", orphanRemoval=true)
     */
    private $impressions;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="movies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->impressions = new ArrayCollection();
    }

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

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getDirector(): ?string
    {
        return $this->director;
    }

    public function setDirector(string $director): self
    {
        $this->director = $director;

        return $this;
    }

    public function getReleaseyear(): ?\DateTimeInterface
    {
        return $this->releaseyear;
    }

    public function setReleaseyear(\DateTimeInterface $releaseyear): self
    {
        $this->releaseyear = $releaseyear;

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
            $impression->setMovie($this);
        }

        return $this;
    }

    public function removeImpression(Impressions $impression): self
    {
        if ($this->impressions->removeElement($impression)) {
            // set the owning side to null (unless already changed)
            if ($impression->getMovie() === $this) {
                $impression->setMovie(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
