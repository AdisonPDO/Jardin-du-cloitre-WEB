<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\CommandeRepository")
 */
class Commande
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Validation;

    /**
     * @ORM\Column(type="datetime")
     */
    private $Date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="Commandes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Menu", inversedBy="Commandes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Menu;

    public function __toString(){
        return strval($this->id);
    }
    public function __construct()
    {
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValidation(): ?bool
    {
        return $this->Validation;
    }

    public function setValidation(bool $Validation): self
    {
        $this->Validation = $Validation;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(\DateTimeInterface $Date): self
    {
        $this->Date = $Date;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $user): self
    {
        $this->User = $user;

        return $this;
    }

    public function getMenu(): ?Menu
    {
        return $this->Menu;
    }

    public function setMenu(?Menu $menu): self
    {
        $this->Menu = $menu;

        return $this;
    }

}
