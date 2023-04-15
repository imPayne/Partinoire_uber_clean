<?php

namespace App\Entity;

use App\Repository\HouseworkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HouseworkRepository::class)]
#[ORM\Table(name: "housework")]
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

    #[ORM\OneToMany(mappedBy: 'housework', targetEntity: Participant::class, cascade: ['remove'])]
    private Collection $Participant;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $list_image = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $Hour = null;

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

    public function addParticipant(Participant $participant): self
    {
        if (!$this->Participant->contains($participant)) {
            $this->Participant[] = $participant;
            $participant->setHousework($this);
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getListImage(): ?string
    {
        return $this->list_image;
    }

    public function setListImage(?string $list_image): self
    {
        $this->list_image = $list_image;

        return $this;
    }

    public function getHour(): ?\DateTimeInterface
    {
        return $this->Hour;
    }

    public function setHour(\DateTimeInterface $Hour): self
    {
        $this->Hour = $Hour;

        return $this;
    }

}
