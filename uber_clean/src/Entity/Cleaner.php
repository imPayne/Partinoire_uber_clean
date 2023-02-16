<?php

namespace App\Entity;

use App\Repository\CleanerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CleanerRepository::class)]
class Cleaner extends User
{
    #[ORM\ManyToMany(targetEntity: Service::class, mappedBy: 'cleaner')]
    private Collection $services;

    public function __construct()
    {
        $this->services = new ArrayCollection();
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->addCleaner($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->removeElement($service)) {
            $service->removeCleaner($this);
        }

        return $this;
    }
}
