<?php

namespace App\Entity;

use App\Repository\ImageArticleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageArticleRepository::class)
 * @ORM\Table(name="image_articles")
 */
class ImageArticle
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
    private $imageName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numImage;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="imageArticles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $article;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getNumImage(): ?int
    {
        return $this->numImage;
    }

    public function setNumImage(?int $numImage): self
    {
        $this->numImage = $numImage;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }
}
