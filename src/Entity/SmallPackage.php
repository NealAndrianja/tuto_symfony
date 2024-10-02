<?php

namespace App\Entity;

use App\Repository\SmallPackageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SmallPackageRepository::class)]
class SmallPackage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $trackingCode = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $receptionDate = null;

    #[ORM\ManyToOne(inversedBy: 'smallPackages')]
    private ?User $customer = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(nullable: true)]
    private ?float $weight = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $dimensions = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume = null;

    #[ORM\ManyToOne(inversedBy: 'smallPackages')]
    private ?DeliveryMode $deliveryMode = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrackingCode(): ?string
    {
        return $this->trackingCode;
    }

    public function setTrackingCode(string $trackingCode): static
    {
        $this->trackingCode = $trackingCode;

        return $this;
    }

    public function getReceptionDate(): ?\DateTimeImmutable
    {
        return $this->receptionDate;
    }

    public function setReceptionDate(\DateTimeImmutable $receptionDate): static
    {
        $this->receptionDate = $receptionDate;

        return $this;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getDimensions(): ?array
    {
        return $this->dimensions;
    }

    public function setDimensions(?array $dimensions): static
    {
        $this->dimensions = $dimensions;

        return $this;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function setVolume(?float $volume): static
    {
        $this->volume = $volume;

        return $this;
    }

    public function getDeliveryMode(): ?DeliveryMode
    {
        return $this->deliveryMode;
    }

    public function setDeliveryMode(?DeliveryMode $deliveryMode): static
    {
        $this->deliveryMode = $deliveryMode;

        return $this;
    }
}
