<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MenuRepository")
 */
class Menu
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", length=250)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commande", mappedBy="Menu")
     */
    private $commandes;

     /**
     * @ORM\OneToMany(targetEntity="App\Entity\Photo", mappedBy="Menu", cascade={"persist", "remove"})
     */
    private $photos;
    /**
     * @ORM\Column(type="integer")
     */
    private $nbPlat;

   
    public function __toString(){
        return strval($this->title);
    }
    
    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->photo     = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection|Commande[]
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

     /**
     * @return Collection|photo[]
     */
    public function getphotos(): Collection
    {
        return $this->photos;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setMenu($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->contains($commande)) {
            $this->commandes->removeElement($commande);
            // set the owning side to null (unless already changed)
            if ($commande->getMenu() === $this) {
                $commande->setMenu(null);
            }
        }

        return $this;
    }

    public function getNbPlat(): ?int
    {
        return $this->nbPlat;
    }

    public function setPlat(int $nbPlat): self
    {
        $this->nbPlat = $nbPlat;

        return $this;
    }

    public function setNbPlat(int $nbPlat): self
    {
        $this->nbPlat = $nbPlat;

        return $this;
    }


}
