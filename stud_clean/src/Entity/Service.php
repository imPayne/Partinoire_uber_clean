<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $icon = null;

    #[ORM\ManyToMany(targetEntity: Cleaner::class, inversedBy: 'services')]
    private Collection $cleaners;

    public function __construct()
    {
        $this->cleaners = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return Collection<int, cleaner>
     */
    public function getCleaners(): Collection
    {
        return $this->cleaners;
    }

    public function addCleaners(Cleaner $cleaner): self
    {
        if (!$this->cleaners->contains($cleaner)) {
            $this->cleaners->add($cleaner);
        }

        return $this;
    }

    public function removeCleaner(cleaner $cleaner): self
    {
        $this->cleaners->removeElement($cleaner);

        return $this;
    }
}