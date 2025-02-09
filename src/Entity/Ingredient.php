<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['ingredient.list', 'ingredient.show', 'stock.create', 'task.show'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['ingredient.list', 'ingredient.show', 'ingredient.update', 'task.show','ingredientPlat.show','ingredientPlat.list'])]
    private ?string $nomIngredient = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['ingredient.list', 'ingredient.show', 'ingredient.update'])]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'idIngredient', targetEntity: Stock::class)]
    private Collection $stockId;

    public function __construct()
    {
        $this->stockId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomIngredient(): ?string
    {
        return $this->nomIngredient;
    }

    public function setNomIngredient(string $nomIngredient): self
    {
        $this->nomIngredient = $nomIngredient;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getStockId(): Collection
    {
        return $this->stockId;
    }

    public function addStockId(Stock $stock): self
    {
        if (!$this->stockId->contains($stock)) {
            $this->stockId[] = $stock;
            $stock->setIdIngredient($this);
        }

        return $this;
    }

    public function removeStockId(Stock $stock): self
    {
        if ($this->stockId->removeElement($stock) && $stock->getIdIngredient() === $this) {
            $stock->setIdIngredient(null);
        }

        return $this;
    }
}
