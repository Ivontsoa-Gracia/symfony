<?php

namespace App\Entity;

use App\Repository\DetailCommandeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Enum\DetailStatu;

#[ORM\Entity(repositoryClass: DetailCommandeRepository::class)]
class DetailCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['detailcommande.create' , 'detailcommande.show'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'detail' , cascade: ['persist'])]
    #[Groups(['detailcommande.create' , 'detailcommande.show' , 'detailcommande.list'])]
    private ?Commande $idCommande = null;

    #[ORM\ManyToOne(inversedBy: 'detailCommande' , cascade: ['persist'])]
    #[Groups(['detailcommande.create' , 'detailcommande.show' , 'detailcommande.list'])]
    private ?Plat $idPlat = null;

    #[ORM\Column(type: 'string', enumType: DetailStatu::class)]
    #[Groups(['detailcommande.create' , 'detailcommande.show' , 'detailcommande.list'])]
    private ?DetailStatu $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCommande(): ?Commande
    {
        return $this->idCommande;
    }

    public function setIdCommande(?Commande $idCommande): static
    {
        $this->idCommande = $idCommande;

        return $this;
    }

    public function getIdPlat(): ?Plat
    {
        return $this->idPlat;
    }

    public function setIdPlat(?Plat $idPlat): static
    {
        $this->idPlat = $idPlat;

        return $this;
    }

    public function getStatus(): ?DetailStatu
    {
        return $this->status;
    }

    public function setStatus(DetailStatu $status): static
    {
        $this->status = $status;
        return $this;
    }
}
