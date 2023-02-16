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

    #[ORM\OneToMany(mappedBy: 'housework', targetEntity: participant::class)]
    private Collection $participant;

    public function __construct()
    {
        $this->participant = new ArrayCollection();
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
     * @return Collection<int, participant>
     */
    public function getParticipant(): Collection
    {
        return $this->participant;
    }

    public function addParticipant(participant $participant): self
    {
        if (!$this->participant->contains($participant)) {
            $this->participant->add($participant);
            $participant->setHousework($this);
        }

        return $this;
    }

    public function removeParticipant(participant $participant): self
    {
        if ($this->participant->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getHousework() === $this) {
                $participant->setHousework(null);
            }
        }

        return $this;
    }
}
