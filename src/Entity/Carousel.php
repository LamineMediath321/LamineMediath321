<?php

namespace App\Entity;

use App\Repository\CarouselRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CarouselRepository::class)
 * @ORM\Table(name="carousels")
 */
class Carousel
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
    private $titre1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titre2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime_immutable",options={"default":"CURRENT_TIMESTAMP"})
     */
    private $cratedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre1(): ?string
    {
        return $this->titre1;
    }

    public function setTitre1(string $titre1): self
    {
        $this->titre1 = $titre1;

        return $this;
    }

    public function getTitre2(): ?string
    {
        return $this->titre2;
    }

    public function setTitre2(?string $titre2): self
    {
        $this->titre2 = $titre2;

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

    public function getCratedAt(): ?\DateTimeImmutable
    {
        return $this->cratedAt;
    }

    public function setCratedAt(\DateTimeImmutable $cratedAt): self
    {
        $this->cratedAt = $cratedAt;

        return $this;
    }
}
