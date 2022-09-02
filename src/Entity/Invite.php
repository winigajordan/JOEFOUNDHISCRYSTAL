<?php

namespace App\Entity;

use App\Repository\InviteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InviteRepository::class)]
class Invite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $photo = null;

    #[ORM\Column(length: 255)]
    private ?string $situation = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'invites')]
    private ?Table $place = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    private ?bool $valide = null;

    #[ORM\OneToOne(mappedBy: 'invite', cascade: ['persist', 'remove'])]
    private ?InvitationsEnvoye $invitationsEnvoye = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $civilite = null;

    #[ORM\OneToOne(mappedBy: 'invite', cascade: ['persist', 'remove'])]
    private ?HerPlace $herPlace = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $herName = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getSituation(): ?string
    {
        return $this->situation;
    }

    public function setSituation(string $situation): self
    {
        $this->situation = $situation;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isValide(): ?bool
    {
        return $this->valide;
    }

    public function setValide(?bool $valide): self
    {
        $this->valide = $valide;

        return $this;
    }

    public function getInvitationsEnvoye(): ?InvitationsEnvoye
    {
        return $this->invitationsEnvoye;
    }

    public function setInvitationsEnvoye(InvitationsEnvoye $invitationsEnvoye): self
    {
        // set the owning side of the relation if necessary
        if ($invitationsEnvoye->getInvite() !== $this) {
            $invitationsEnvoye->setInvite($this);
        }

        $this->invitationsEnvoye = $invitationsEnvoye;

        return $this;
    }

    public function getCivilite(): ?string
    {
        return $this->civilite;
    }

    public function setCivilite(?string $civilite): self
    {
        $this->civilite = $civilite;

        return $this;
    }

    public function getHerPlace(): ?HerPlace
    {
        return $this->herPlace;
    }

    public function setHerPlace(HerPlace $herPlace): self
    {
        // set the owning side of the relation if necessary
        if ($herPlace->getInvite() !== $this) {
            $herPlace->setInvite($this);
        }

        $this->herPlace = $herPlace;

        return $this;
    }

    public function getHerName(): ?string
    {
        return $this->herName;
    }

    public function setHerName(?string $herName): self
    {
        $this->herName = $herName;

        return $this;
    }


}
