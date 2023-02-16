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

    #[ORM\ManyToMany(targetEntity: cleaner::class, inversedBy: 'services')]
    private Collection $cleaner;

    public function __construct()
    {
        $this->cleaner = new ArrayCollection();
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
    public function getCleaner(): Collection
    {
        return $this->cleaner;
    }

    public function addCleaner(cleaner $cleaner): self
    {
        if (!$this->cleaner->contains($cleaner)) {
            $this->cleaner->add($cleaner);
        }

        return $this;
    }

    public function removeCleaner(cleaner $cleaner): self
    {
        $this->cleaner->removeElement($cleaner);

        return $this;
    }
}
