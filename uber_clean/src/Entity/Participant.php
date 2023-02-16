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

    #[ORM\ManyToOne(inversedBy: 'participant')]
    private ?Housework $housework = null;

    #[ORM\ManyToOne]
    private ?service $service = null;

    #[ORM\ManyToOne]
    private ?cleaner $cleaner = null;

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

    public function getService(): ?service
    {
        return $this->service;
    }

    public function setService(?service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getCleaner(): ?cleaner
    {
        return $this->cleaner;
    }

    public function setCleaner(?cleaner $cleaner): self
    {
        $this->cleaner = $cleaner;

        return $this;
    }
}
