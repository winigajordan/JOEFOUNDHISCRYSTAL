<?php

namespace App\Entity;

use App\Repository\CoupleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoupleRepository::class)]
class Couple extends Invite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $herName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHerName(): ?string
    {
        return $this->herName;
    }

    public function setHerName(string $herName): self
    {
        $this->herName = $herName;

        return $this;
    }
}
