<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MovieRepository")
 */
class Movie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank( message = "Veuillez saisir le titre du film")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"title"})
     * #@Assert\NotBlank( message = "Veuillez saisir le slug de la page")
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank( message = "Veuillez ajouter l'affiche du film.")
     */
    private $poster;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank( message = "Veuillez saisir la date de sortie en salle")
     */
    private $releasedAt;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank( message = "Veuillez saisir le synopsys du film")
     */
    private $synopsys;

    /**
     * @todo Assert Choice - établir un nombre min/max de catégories et acteurs ???
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", mappedBy="movies")
     * #@Assert\Choice( callback="getCategories", min = 1, max = 3, minMessage = "Veuillez choisir au moins une catégorie", maxMessage = "Choisir jusqu'à 3 catégories au maximum")
     * @ORM\OrderBy({"title" = "ASC"})
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\People", mappedBy="actedIn")
     */
    private $actors;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\People", inversedBy="directed")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\OrderBy({"lastName" = "ASC"})
     * @Assert\NotBlank( message = "Veuillez sélectionner le réalisateur du film")
     */
    private $director;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Rating", mappedBy="movie")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $ratings;


    private $notation = false;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->actors = new ArrayCollection();
        $this->ratings = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    public function getNotation()
    {
        if ($this->notation === false) {
            $ratings = $this->getRatings();
            if (count($ratings)) {
                $notation = 0;
                foreach ($ratings as $rating) {
                    $notation += $rating->getNotation();
                }
                $this->notation = $notation / count($ratings);
            }
        }
        return $this->notation;
    }

    public function setMovieNotation(float $notation)
    {
        $this->notation = $notation;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(string $poster): self
    {
        $this->poster = $poster;

        return $this;
    }

    public function getReleasedAt(): ?\DateTimeInterface
    {
        return $this->releasedAt;
    }

    public function setReleasedAt(\DateTimeInterface $releasedAt): self
    {
        $this->releasedAt = $releasedAt;

        return $this;
    }

    public function getSynopsys(): ?string
    {
        return $this->synopsys;
    }

    public function setSynopsys(string $synopsys): self
    {
        $this->synopsys = $synopsys;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addMovie($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            $category->removeMovie($this);
        }

        return $this;
    }

    /**
     * @return Collection|People[]
     */
    public function getActors(): Collection
    {
        return $this->actors;
    }

    public function addActor(People $actor): self
    {
        if (!$this->actors->contains($actor)) {
            $this->actors[] = $actor;
            $actor->addActedIn($this);
        }

        return $this;
    }

    public function removeActor(People $actor): self
    {
        if ($this->actors->contains($actor)) {
            $this->actors->removeElement($actor);
            $actor->removeActedIn($this);
        }

        return $this;
    }

    public function getDirector(): ?People
    {
        return $this->director;
    }

    public function setDirector(?People $director): self
    {
        $this->director = $director;

        return $this;
    }

    /**
     * @return Collection|Rating[]
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings[] = $rating;
            $rating->setMovie($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): self
    {
        if ($this->ratings->contains($rating)) {
            $this->ratings->removeElement($rating);
            // set the owning side to null (unless already changed)
            if ($rating->getMovie() === $this) {
                $rating->setMovie(null);
            }
        }

        return $this;
    }

    public function getMainActors(): array
    {
        return $this->actors->slice(0, 2);
    }
}
