<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use App\Entity\Traits\Timestampable;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="users")
 *@Vich\Uploadable
 *@ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface,\Serializable
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
     *@Assert\NotBlank(message="Le prenom ne doit pas etre vide")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     *@Assert\NotBlank(message="Le nom ne doit pas être vide")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=180, unique=true,nullable=true)
     *@Assert\NotBlank(message="L' adresse email ne doit pas être vide")
     *@Assert\Email(message="Veillez entrer une adresse email valide")
     */
    private $email;


    /**
     * @ORM\Column(type="string", length=255,unique=true)
     * @Assert\Regex(pattern="#^\+|(00)221( ?[0-9]){7,}$#",message="Veillez entrer un numéro correct")
     */
    private $phone;


     /**
     * @ORM\Column(type="text", length=255)
     * @Assert\NotBlank(message="L'adresse ne doit pas être vide")
     */
    private $adresse;


    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="user_image", fileNameProperty="imageName")
     * 
     * @var File|null
     */
    private $imageFile;


    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $imageName;

     
    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="user")
     */
    private $articles;

    /**
     * @ORM\OneToOne(targetEntity=Banque::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $banque;

    /**
     * @ORM\OneToOne(targetEntity=Store::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $store;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=LadiaMessage::class, mappedBy="destinataire")
     */
    private $ladiaMessages;

    /**
     * @ORM\OneToMany(targetEntity=ArticleLike::class, mappedBy="user")
     */
    private $likes;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $aboutMe;

    /**
     * @ORM\OneToMany(targetEntity=Commentaire::class, mappedBy="user")
     */
    private $commentaires;

    

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->ladiaMessages = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
    }

   

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }



    
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }


    /**
    *Permet de savoir si le user a des messages
    */
    public function messageNoLus() :int
    {
        $i = 0;
        foreach ($this->ladiaMessages as $message) {
            if ($message->getDestinataire() === $this && $message->getEstLu()===false) $i++;
        }
        return $i;
    }


    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }


    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }
 

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->setUpdatedAt( new \DateTimeImmutable);
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setUser($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getUser() === $this) {
                $article->setUser(null);
            }
        }

        return $this;
    }

    public function getBanque(): ?Banque
    {
        return $this->banque;
    }

    public function setBanque(?Banque $banque): self
    {
        // unset the owning side of the relation if necessary
        if ($banque === null && $this->banque !== null) {
            $this->banque->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($banque !== null && $banque->getUser() !== $this) {
            $banque->setUser($this);
        }

        $this->banque = $banque;

        return $this;
    }

    public function serialize()
    {
        return serialize(array(
                    $this->id,
                    $this->firstName,
                    $this->lastName,
                    $this->email,
                    $this->phone,
                    $this->adresse,
                    $this->roles,
                    $this->password,
                    $this->imageName,
                    $this->articles,
                    $this->banque,
        ));
    }

    public function unserialize($serialized)
    {
        list(
                    $this->id,
                    $this->firstName,
                    $this->lastName,
                    $this->email,
                    $this->phone,
                    $this->adresse,
                    $this->roles,
                    $this->password,
                    $this->imageName,
                    $this->articles,
                    $this->banque,
        ) = unserialize($serialized);
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function setStore(?Store $store): self
    {
        // unset the owning side of the relation if necessary
        if ($store === null && $this->store !== null) {
            $this->store->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($store !== null && $store->getUser() !== $this) {
            $store->setUser($this);
        }

        $this->store = $store;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

   
   public function gravatar(?int $size = 200)
    {
        return 'https://www.gravatar.com/avatar/'.md5(strtolower(trim($this->getEmail()))).'/?s='.$size;
    }

   /**
    * @return Collection|LadiaMessage[]
    */
   public function getLadiaMessages(): Collection
   {
       return $this->ladiaMessages;
   }

   public function addLadiaMessage(LadiaMessage $ladiaMessage): self
   {
       if (!$this->ladiaMessages->contains($ladiaMessage)) {
           $this->ladiaMessages[] = $ladiaMessage;
           $ladiaMessage->setDestinataire($this);
       }

       return $this;
   }

   public function removeLadiaMessage(LadiaMessage $ladiaMessage): self
   {
       if ($this->ladiaMessages->removeElement($ladiaMessage)) {
           // set the owning side to null (unless already changed)
           if ($ladiaMessage->getDestinataire() === $this) {
               $ladiaMessage->setDestinataire(null);
           }
       }

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
           $like->setUser($this);
       }

       return $this;
   }

   public function removeLike(ArticleLike $like): self
   {
       if ($this->likes->removeElement($like)) {
           // set the owning side to null (unless already changed)
           if ($like->getUser() === $this) {
               $like->setUser(null);
           }
       }

       return $this;
   }

   public function getAboutMe(): ?string
   {
       return $this->aboutMe;
   }

   public function setAboutMe(?string $aboutMe): self
   {
       $this->aboutMe = $aboutMe;

       return $this;
   }

   /*
   Permet de compter le nombre like
    */
   public function counLikesArticlesUser(): int 
   {
        $count = 0;
        foreach ($this->articles as $article) {
            $count += $article->getLikes()->count();
        }
        return $count;
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
           $commentaire->setUser($this);
       }

       return $this;
   }

   public function removeCommentaire(Commentaire $commentaire): self
   {
       if ($this->commentaires->removeElement($commentaire)) {
           // set the owning side to null (unless already changed)
           if ($commentaire->getUser() === $this) {
               $commentaire->setUser(null);
           }
       }

       return $this;
   }



}
