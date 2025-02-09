<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Enum\StockStatu;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['stock.create', 'stock.list', 'task.show'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['stock.create', 'stock.list', 'task.show'])]
    private ?int $quantite = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['stock.create'])]
    private ?\DateTimeInterface $dateMouvement = null;

    #[ORM\ManyToOne(inversedBy: 'stockId')]
    #[Groups(['stock.create', 'stock.list', 'task.show'])]
    private ?Ingredient $idIngredient = null;

    #[ORM\Column(type: 'string', enumType: StockStatu::class)]
    #[Groups(['stock.create', 'stock.list', 'task.show'])]
    private ?StockStatu $status = null;

    public function __construct()
    {
        $this->status = StockStatu::ENTREE;
    }

    // Getters et setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }

    public function getDateMouvement(): ?\DateTimeInterface
    {
        return $this->dateMouvement;
    }

    public function setDateMouvement(\DateTimeInterface $dateMouvement): self
    {
        $this->dateMouvement = $dateMouvement;
        return $this;
    }

    public function getIdIngredient(): ?Ingredient
    {
        return $this->idIngredient;
    }

    public function setIdIngredient(?Ingredient $idIngredient): self
    {
        $this->idIngredient = $idIngredient;
        return $this;
    }

    public function getStatus(): ?StockStatu
    {
        return $this->status;
    }

    public function setStatus(StockStatu $status): self
    {
        $this->status = $status;
        return $this;
    }
}
