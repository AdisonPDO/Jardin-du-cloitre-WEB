<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhotoRepository")
 */
class Photo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $photo1;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $photo2;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $photo3;

   /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Menu", inversedBy="photos")
     * @ORM\JoinColumn(nullable=true)
     */
    private $Menu;

    public function __toString(){
        return strval($this->id);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhoto1(): ?string
    {
        return $this->photo1;
    }
    

    public function setPhoto1(?string $photo1): self
    {
        $this->photo1 = $photo1;

        return $this;
    }

    public function getPhoto2(): ?string
    {
        return $this->photo2;
    }

    public function setPhoto2(?string $photo2): self
    {
        $this->photo2 = $photo2;

        return $this;
    }

    public function getPhoto3(): ?string
    {
        return $this->photo3;
    }

    public function setPhoto3(?string $photo3): self
    {
        $this->photo3 = $photo3;

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
