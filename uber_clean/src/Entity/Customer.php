<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer extends User
{
    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Housework::class, orphanRemoval: true)]
    private Collection $houseworks;

    public function __construct()
    {
        $this->houseworks = new ArrayCollection();
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return Collection<int, Housework>
     */
    public function getHouseworks(): Collection
    {
        return $this->houseworks;
    }

    public function addHousework(Housework $housework): self
    {
        if (!$this->houseworks->contains($housework)) {
            $this->houseworks->add($housework);
            $housework->setCustomer($this);
        }

        return $this;
    }

    public function removeHousework(Housework $housework): self
    {
        if ($this->houseworks->removeElement($housework)) {
            // set the owning side to null (unless already changed)
            if ($housework->getCustomer() === $this) {
                $housework->setCustomer(null);
            }
        }

        return $this;
    }
}
