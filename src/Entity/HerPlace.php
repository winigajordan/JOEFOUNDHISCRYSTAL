<?php

namespace App\Entity;

use App\Repository\HerPlaceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HerPlaceRepository::class)]
class HerPlace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'herPlaces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Table $place = null;

    #[ORM\OneToOne(inversedBy: 'herPlace')]
    private ?Invite $invite = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlace(): ?Table
    {
        return $this->place;
    }

    public function setPlace(?Table $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getInvite(): ?Invite
    {
        return $this->invite;
    }

    public function setInvite(?Invite $invite): self
    {
        $this->invite = $invite;

        return $this;
    }

}
