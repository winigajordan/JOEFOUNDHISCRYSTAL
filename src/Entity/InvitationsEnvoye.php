<?php

namespace App\Entity;

use App\Repository\InvitationsEnvoyeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvitationsEnvoyeRepository::class)]
class InvitationsEnvoye
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'invitationsEnvoye', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Invite $invite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvite(): ?Invite
    {
        return $this->invite;
    }

    public function setInvite(Invite $invite): self
    {
        $this->invite = $invite;

        return $this;
    }
}
