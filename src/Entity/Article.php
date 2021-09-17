<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @ORM\Table(name="articles")
 *@ORM\HasLifecycleCallbacks
 */
class Article
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
    private $nomArticle;

    /**
     * @ORM\Column(type="text", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="text", length=255)
     */
    private $lieuVente;

    /**
     * @ORM\OneToMany(targetEntity=ImageArticle::class, mappedBy="article", orphanRemoval=true)
     */
    private $imageArticles;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=SousCategorie::class, inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sousCategorie;

    /**
     * @ORM\Column(type="datetime_immutable",options={"default":"CURRENT_TIMESTAMP"})
     */
    private $createdAt;


    public function __construct()
    {
        $this->imageArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomArticle(): ?string
    {
        return $this->nomArticle;
    }

    public function setNomArticle(string $nomArticle): self
    {
        $this->nomArticle = $nomArticle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLieuVente(): ?string
    {
        return $this->lieuVente;
    }

    public function setLieuVente(string $lieuVente): self
    {
        $this->lieuVente = $lieuVente;

        return $this;
    }

    /**
     * @return Collection|ImageArticle[]
     */
    public function getImageArticles(): Collection
    {
        return $this->imageArticles;
    }

    public function addImageArticle(ImageArticle $imageArticle): self
    {
        if (!$this->imageArticles->contains($imageArticle)) {
            $this->imageArticles[] = $imageArticle;
            $imageArticle->setArticle($this);
        }

        return $this;
    }

    public function removeImageArticle(ImageArticle $imageArticle): self
    {
        if ($this->imageArticles->removeElement($imageArticle)) {
            // set the owning side to null (unless already changed)
            if ($imageArticle->getArticle() === $this) {
                $imageArticle->setArticle(null);
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

    public function getSousCategorie(): ?SousCategorie
    {
        return $this->sousCategorie;
    }

    public function setSousCategorie(?SousCategorie $sousCategorie): self
    {
        $this->sousCategorie = $sousCategorie;

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

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {

        if ($this->getCreatedAt()===null) {
            $this->setCreatedAt(new\DateTimeImmutable);
        }
    }
}
