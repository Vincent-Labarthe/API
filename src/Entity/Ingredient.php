<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IngredientRepository::class)
 */
class Ingredient
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Recette::class, inversedBy="ingredients")
     */
    private $Recette;

    public function __construct()
    {
        $this->Recette = new ArrayCollection();
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

    /**
     * @return Collection|Recette[]
     */
    public function getRecette(): Collection
    {
        return $this->Recette;
    }

    public function addRecette(Recette $recette): self
    {
        if (!$this->Recette->contains($recette)) {
            $this->Recette[] = $recette;
        }

        return $this;
    }

    public function removeRecette(Recette $recette): self
    {
        if ($this->Recette->contains($recette)) {
            $this->Recette->removeElement($recette);
        }

        return $this;
    }
}
