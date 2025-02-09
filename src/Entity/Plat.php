<?php

namespace App\Entity;

use App\Repository\PlatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PlatRepository::class)]
class Plat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['plats.create','plats.list', 'plats.show'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['plats.list', 'plats.show', 'plats.update', 'plats.create','ingredientPlat.show','ingredientPlat.list'])]
    private ?string $nomPlat = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['plats.list', 'plats.show', 'plats.update', 'plats.create','ingredientPlat.list'])]
    private ?string $prixUnitaire = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups(['plats.list' , 'plats.show', 'plats.update', 'plats.create','ingredientPlat.list'])]
    private ?\DateTimeInterface $tempsCuisson = null;

    /**
     * @var Collection<int, IngredientPlat>
     */
    #[ORM\OneToMany(targetEntity: IngredientPlat::class, mappedBy: 'plat', cascade: ['persist', 'remove'])]
    private Collection $ingredientPlats;

    /**
     * @var Collection<int, DetailCommande>
     */
    #[ORM\OneToMany(targetEntity: DetailCommande::class, mappedBy: 'idPlat')]
    private Collection $detailCommande;

    #[ORM\Column(length: 255)]
    #[Groups(['plats.list' , 'plats.show', 'plats.update', 'plats.create','ingredientPlat.list'])]

    private ?string $image = null;

    public function __construct()
    {
        $this->ingredientPlats = new ArrayCollection();
        $this->detailCommande = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPlat(): ?string
    {
        return $this->nomPlat;
    }

    public function setNomPlat(string $nomPlat): static
    {
        $this->nomPlat = $nomPlat;

        return $this;
    }

    public function getPrixUnitaire(): ?string
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(string $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;

        return $this;
    }

    public function getTempsCuisson(): ?\DateTimeInterface
    {
        return $this->tempsCuisson;
    }

    public function setTempsCuisson(\DateTimeInterface $tempsCuisson): static
    {
        $this->tempsCuisson = $tempsCuisson;

        return $this;
    }

    /**
     * @return Collection<int, IngredientPlat>
     */
    public function getIngredientPlats(): Collection
    {
        return $this->ingredientPlats;
    }

    public function addIngredientPlat(IngredientPlat $ingredientPlat): static
    {
        if (!$this->ingredientPlats->contains($ingredientPlat)) {
            $this->ingredientPlats->add($ingredientPlat);
            $ingredientPlat->setPlat($this);
        }

        return $this;
    }

    public function removeIngredientPlat(IngredientPlat $ingredientPlat): static
    {
        if ($this->ingredientPlats->removeElement($ingredientPlat)) {
            // set the owning side to null (unless already changed)
            if ($ingredientPlat->getPlat() === $this) {
                $ingredientPlat->setPlat(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DetailCommande>
     */
    public function getDetailCommande(): Collection
    {
        return $this->detailCommande;
    }

    public function addDetailCommande(DetailCommande $detailCommande): static
    {
        if (!$this->detailCommande->contains($detailCommande)) {
            $this->detailCommande->add($detailCommande);
            $detailCommande->setIdPlat($this);
        }

        return $this;
    }

    public function removeDetailCommande(DetailCommande $detailCommande): static
    {
        if ($this->detailCommande->removeElement($detailCommande)) {
            // set the owning side to null (unless already changed)
            if ($detailCommande->getIdPlat() === $this) {
                $detailCommande->setIdPlat(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }
}
