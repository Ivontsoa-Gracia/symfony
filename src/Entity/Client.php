<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['client.list', 'client.show' , 'client.create'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['client.list', 'client.show', 'client.update' , 'client.create' , 'commande.list' , 'detailCommande.list'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['client.create'])]
    private ?string $password = null; 

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $apiToken = null;

    /**
     * @var Collection<int, Commande>
     */
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'idclient')]
    private Collection $commandeRelat;

    public function __construct()
    {
        $this->commandeRelat = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string  
    {
        return $this->password;
    }

    public function setPassword(string $password): static  
    {
        $this->password = $password;
        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(?string $apiToken): static
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // Cette méthode est utilisée pour effacer les informations sensibles (par exemple, le mot de passe)
        $this->password = null; // Effacer le mot de passe après authentification
    }

    public function getUserIdentifier(): string  
    {
        return $this->email;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandeRelat(): Collection
    {
        return $this->commandeRelat;
    }

    public function addCommandeRelat(Commande $commandeRelat): static
    {
        if (!$this->commandeRelat->contains($commandeRelat)) {
            $this->commandeRelat->add($commandeRelat);
            $commandeRelat->setIdclient($this);
        }

        return $this;
    }

    public function removeCommandeRelat(Commande $commandeRelat): static
    {
        if ($this->commandeRelat->removeElement($commandeRelat)) {
            if ($commandeRelat->getIdclient() === $this) {
                $commandeRelat->setIdclient(null);
            }
        }

        return $this;
    }
}
