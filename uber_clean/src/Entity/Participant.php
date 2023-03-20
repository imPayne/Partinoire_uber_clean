<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'Participant')]
    private ?Housework $housework = null;

    #[ORM\ManyToOne]
    private ?Service $Service = null;

    #[ORM\ManyToOne]
    private ?Cleaner $Cleaner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHousework(): ?Housework
    {
        return $this->housework;
    }

    public function setHousework(?Housework $housework): self
    {
        $this->housework = $housework;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->Service;
    }

    public function setService(?Service $Service): self
    {
        $this->Service = $Service;

        return $this;
    }

    public function getCleaner(): ?cleaner
    {
        return $this->Cleaner;
    }

    public function setCleaner(?cleaner $Cleaner): self
    {
        $this->Cleaner = $Cleaner;

        return $this;
    }
}
