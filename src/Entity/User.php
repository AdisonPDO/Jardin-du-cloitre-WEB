<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 * fields={"mail"},
 * message="l'adresse mail est deja utilisée"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\Length(
     * min = 10, 
     * max = 10, 
     * minMessage = "veuillez entrer un numero de telephone , ex : 0612457893", 
     * maxMessage = "veuillez entrer un numero de telephone , ex : 0612457893")
     *@Assert\Regex(
     *pattern="/^(?:(?:\+|00)33[\s.-]{0,3}(?:\(0\)[\s.-]{0,3})?|0)[1-9](?:(?:[\s.-]?\d{2}){4}|\d{2}(?:[\s.-]?\d{3}){2})$/", 
     *message="format de numéro incorrect")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\Email(
     * message = " l\'adresse mail :' {{ value }} ' n\'est pas valide ")
     * 
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     * min = 6, 
     * max = 255, 
     * minMessage = "votre mot de passe doit contenir au minimum 6 caracteres") 
     *@Assert\EqualTo(
     * propertyPath="confirmPassword",
     *message="la confirmation de votre mot de passe n'est pas valide")
     */
    private $password;

    public $confirmPassword;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commande", mappedBy="User")
     */
    private $commandes;

    /**
     * @ORM\Column(type="array")
     */
    public $roles = [];

    public function __toString(){
        return $this->getUsername();
    }

   
    public function __construct()
    {
        $this->commandes = new ArrayCollection();
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

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

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

    public function eraseCredentials(){}

    public function getSalt(){}

   

    public function getUsername(){
        return $this->name;
    }

    /**
     * @return Collection|Commande[]
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setUser($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->contains($commande)) {
            $this->commandes->removeElement($commande);
            // set the owning side to null (unless already changed)
            if ($commande->getUser() === $this) {
                $commande->setUser(null);
            }
        }

        return $this;
    }

    public function getRoles() {
        if (empty($this->roles)) {
             return ['ROLE_USER'];
        }
        return $this->roles;
    }

    public function addRole($roles) {
        $this->roles[] = $roles;
   
        return $this;
    }
    
      /**
     * @var string le token qui servira lors de l'oubli de mot de passe
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $resetToken;

    /**
     * @ORM\Column(type="boolean")
     */
    private $newsletter;

    /**
     * @return string
     */
    public function getResetToken(): string
    {
        if($this->resetToken==null){
            return "";
        } 
        return $this->resetToken;
    }

    /**
     * @param string $resetToken
     */
    public function setResetToken(?string $resetToken): void
    {
        $this->resetToken = $resetToken;
    }


    public function getNewsletter(): ?bool
    {
        return $this->newsletter;
    }

    public function setNewsletter(bool $newsletter): self
    {
        $this->newsletter = $newsletter;

        return $this;
    }

   
}
