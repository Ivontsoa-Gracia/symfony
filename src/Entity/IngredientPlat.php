<?php

namespace App\Entity;

use App\Repository\IngredientPlatRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: IngredientPlatRepository::class)]
class IngredientPlat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['ingredientPlat.list', 'ingredientPlat.show', 'ingredientPlat.update'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Plat::class, inversedBy: 'ingredientPlats', cascade: ['persist'])]
    #[Assert\NotNull(groups: ['create', 'update'])]
    #[Groups(['ingredientPlat.list', 'ingredientPlat.show', 'ingredientPlat.update'])]
    private ?Plat $plat = null;

    #[ORM\ManyToOne(targetEntity: Ingredient::class, inversedBy: 'ingredientPlats', cascade: ['persist'])]
    #[Assert\NotNull(groups: ['create', 'update'])]
    #[Groups(['ingredientPlat.list', 'ingredientPlat.show', 'ingredientPlat.update'])]
    private ?Ingredient $ingredient = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlat(): ?Plat
    {
        return $this->plat;
    }

    public function setPlat(?Plat $plat): static
    {
        $this->plat = $plat;

        return $this;
    }

    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(?Ingredient $ingredient): static
    {
        $this->ingredient = $ingredient;

        return $this;
    }
}
