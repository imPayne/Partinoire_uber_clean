<?php

namespace App\Entity;

use App\Repository\CleanerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CleanerRepository::class)]
class Cleaner extends User
{
    #[ORM\ManyToMany(targetEntity: Service::class, mappedBy: 'cleaners')]
    private Collection $services;

    #[ORM\Column(type: Types::DECIMAL, precision: 2, scale: 1, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(length: 15)]
    private ?string $PhoneNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $student_proof = null;

    #[ORM\Column]
    private ?bool $checked = null;

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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->PhoneNumber;
    }

    public function setPhoneNumber(string $PhoneNumber): self
    {
        $this->PhoneNumber = $PhoneNumber;

        return $this;
    }

    public function getStudentProof(): ?string
    {
        return $this->student_proof;
    }

    public function setStudentProof(string $student_proof): self
    {
        $this->student_proof = $student_proof;

        return $this;
    }

    public function isChecked(): ?bool
    {
        return $this->checked;
    }

    public function setChecked(bool $checked): self
    {
        $this->checked = $checked;

        return $this;
    }
}
