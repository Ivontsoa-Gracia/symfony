<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\StatuCommande;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['commande.create' ,'detailcommande.list'])]
    private ?int $id = null;
    
    #[ORM\ManyToOne(inversedBy: 'commandeRelat', cascade: ['persist'])]
    #[Groups(['commande.create' , 'commande.show' , 'commande.list' ,'commande.update' , 'commande.delete' , 'detailcommande.list'])]
    private ?Client $idclient = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['commande.create' , 'commande.show' , 'commande.list' ,'commande.update' , 'commande.delete' , 'detailcommande.list'])]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['commande.create' , 'commande.show' , 'commande.list' ,'commande.update' , 'commande.delete' , ])]
    private ?string $montantTotal = null;

    #[ORM\Column(type: 'string', enumType: StatuCommande::class)]
    #[Groups(['commande.create' , 'commande.show' , 'commande.list' ,'commande.update' , 'commande.delete'])]
    private ?StatuCommande $status = null;

    /**
     * @var Collection<int, DetailCommande>
     */
    #[ORM\OneToMany(targetEntity: DetailCommande::class, mappedBy: 'idCommande')]
    private Collection $detail;

    public function __construct()
    {
        $this->status = StatuCommande::EN_COURS;
        $this->detail = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdclient(): ?Client
    {
        return $this->idclient;
    }

    public function setIdclient(?Client $idclient): static
    {
        $this->idclient = $idclient;
        return $this;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): static
    {
        $this->dateCommande = $dateCommande;
        return $this;
    }

    public function getMontantTotal(): ?string
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(string $montantTotal): static
    {
        $this->montantTotal = $montantTotal;
        return $this;
    }

    public function getStatus(): ?StatuCommande
    {
        return $this->status;
    }

    public function setStatus(StatuCommande $status): static
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return Collection<int, DetailCommande>
     */
    public function getDetail(): Collection
    {
        return $this->detail;
    }

    public function addDetail(DetailCommande $detail): static
    {
        if (!$this->detail->contains($detail)) {
            $this->detail->add($detail);
            $detail->setIdCommande($this);
        }

        return $this;
    }

    public function removeDetail(DetailCommande $detail): static
    {
        if ($this->detail->removeElement($detail)) {
            // set the owning side to null (unless already changed)
            if ($detail->getIdCommande() === $this) {
                $detail->setIdCommande(null);
            }
        }

        return $this;
    }
}
