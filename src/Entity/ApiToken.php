<?php

namespace App\Entity;

use App\Repository\ApiTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiTokenRepository::class)]
class ApiToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tokenString = null;

    #[ORM\Column(nullable: true)]
    private ?int $Expiration = null;

    #[ORM\ManyToOne(inversedBy: 'apiTokens')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTokenString(): ?string
    {
        return $this->tokenString;
    }

    public function setTokenString(?string $tokenString): static
    {
        $this->tokenString = $tokenString;

        return $this;
    }

    public function getExpiration(): ?int
    {
        return $this->Expiration;
    }

    public function setExpiration(?int $Expiration): static
    {
        $this->Expiration = $Expiration;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
