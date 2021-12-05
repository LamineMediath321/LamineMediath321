<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use App\Entity\Traits\Timestampable;
use App\Entity\User;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @ORM\Table(name="articles", indexes={@ORM\Index(columns={"nom_article", "description"},flags={"fulltext"})})
 *@ORM\HasLifecycleCallbacks
 */
class Article
{
    use Timestampable;
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $etoiles;

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
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $choixVisbilite;

    /**
     * @ORM\Column(type="boolean")
     */
    private $estPaye;

    /**
     * @ORM\OneToMany(targetEntity=ArticleLike::class, mappedBy="article")
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity=LadiaMessage::class, mappedBy="article")
     */
    private $messages;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbVues;

    /**
     * @ORM\OneToMany(targetEntity=Commentaire::class, mappedBy="article")
     */
    private $commentaires;

    




    public function __construct()
    {
        $this->imageArticles = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
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

    public function setDescription(?string $description): self
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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getEtoiles(): ?int
    {
        return $this->etoiles;
    }

    public function setEtoiles(?int $etoiles): self
    {
        $this->etoiles = $etoiles;

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

    public function getChoixVisbilite(): ?string
    {
        return $this->choixVisbilite;
    }

    public function setChoixVisbilite(?string $choixVisbilite): self
    {
        $this->choixVisbilite = $choixVisbilite;

        return $this;
    }

    public function getEstPaye(): ?bool
    {
        return $this->estPaye;
    }

    public function setEstPaye(bool $estPaye): self
    {
        $this->estPaye = $estPaye;

        return $this;
    }

    /**
     * @return Collection|ArticleLike[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(ArticleLike $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setArticle($this);
        }

        return $this;
    }

    public function removeLike(ArticleLike $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getArticle() === $this) {
                $like->setArticle(null);
            }
        }

        return $this;
    }

    /**
    *Permet de savoir si cette article est like par un user
    */
    public function isLikedByUser(?User $user) :bool
    {
        foreach ($this->likes as $like) {
            if ($like->getUser() === $user) return true;
        }
        
        return false;
    }

    public function getNbVues(): ?int
    {
        return $this->nbVues;
    }

    public function setNbVues(int $nbVues): self
    {
        $this->nbVues = $nbVues;

        return $this;
    }

    /**
     * @return Collection|LadiaMessage[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(LadiaMessage $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setArticle($this);
        }

        return $this;
    }

    public function removeMessage(LadiaMessage $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getArticle() === $this) {
                $message->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Commentaire[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setArticle($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getArticle() === $this) {
                $commentaire->setArticle(null);
            }
        }

        return $this;
    }


    

}
