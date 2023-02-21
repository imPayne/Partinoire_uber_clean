<?php

namespace App\Entity;

use App\Repository\HouseworkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HouseworkRepository::class)]
class Housework
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateStart = null;

    #[ORM\ManyToOne(inversedBy: 'houseworks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    #[ORM\OneToMany(mappedBy: 'housework', targetEntity: Participant::class)]
    private Collection $Participant;

    public function __construct()
    {
        $this->Participant = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipant(): Collection
    {
        return $this->Participant;
    }

    public function addParticipant(Participant $Participant): self
    {
        if (!$this->Participant->contains($Participant)) {
            $this->Participant->add($Participant);
            $Participant->setHousework($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $Participant): self
    {
        if ($this->Participant->removeElement($Participant)) {
            // set the owning side to null (unless already changed)
            if ($Participant->getHousework() === $this) {
                $Participant->setHousework(null);
            }
        }

        return $this;
    }
}
