<?php

namespace App\Entity;

use App\Repository\BanqueRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;

/**
 *@ORM\Entity(repositoryClass=BanqueRepository::class)
 *@ORM\Table(name="banques")
 *@ORM\HasLifecycleCallbacks
 */
class Banque
{
    use Timestampable;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $pieces;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="banque", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numeroCompte;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPieces(): ?int
    {
        return $this->pieces;
    }

    public function setPieces(?int $pieces): self
    {
        $this->pieces = $pieces;

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

    public function getNumeroCompte(): ?string
    {
        return $this->numeroCompte;
    }

    public function setNumeroCompte(?string $numeroCompte): self
    {
        $this->numeroCompte = $numeroCompte;

        return $this;
    }
}
