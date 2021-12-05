<?php

namespace App\Entity;

use App\Repository\LadiaMessageRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;  

/**
 * @ORM\Entity(repositoryClass=LadiaMessageRepository::class)
 * @ORM\Table(name="ladiaMessages")
 * @ORM\HasLifecycleCallbacks
 */
class LadiaMessage
{
    use Timestampable;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")

     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ladiaMessages")
     */
    private $destinataire;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="text")
     */
    private $coordonnees;

    /**
     * @ORM\Column(type="boolean")
     */
    private $estLu;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="messages")
     */
    private $article;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDestinataire(): ?User
    {
        return $this->destinataire;
    }

    public function setDestinataire(?User $destinataire): self
    {
        $this->destinataire = $destinataire;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getCoordonnees(): ?string
    {
        return $this->coordonnees;
    }

    public function setCoordonnees(string $coordonnees): self
    {
        $this->coordonnees = $coordonnees;

        return $this;
    }

    public function getEstLu(): ?bool
    {
        return $this->estLu;
    }

    public function setEstLu(bool $estLu): self
    {
        $this->estLu = $estLu;

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
