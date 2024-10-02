<?php

namespace App\Entity;

use App\Enums\DeliveryModeType;
use App\Repository\DeliveryModeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeliveryModeRepository::class)]
class DeliveryMode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true, enumType: DeliveryModeType::class)]
    private ?DeliveryModeType $mode = null;

    #[ORM\Column(nullable: true)]
    private ?int $fee = null;

    /**
     * @var Collection<int, SmallPackage>
     */
    #[ORM\OneToMany(targetEntity: SmallPackage::class, mappedBy: 'deliveryMode')]
    private Collection $smallPackages;

    public function __construct()
    {
        $this->smallPackages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMode(): ?DeliveryModeType
    {
        return $this->mode;
    }

    public function setMode(?DeliveryModeType $mode): static
    {
        $this->mode = $mode;

        return $this;
    }

    public function getFee(): ?int
    {
        return $this->fee;
    }

    public function setFee(?int $fee): static
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * @return Collection<int, SmallPackage>
     */
    public function getSmallPackages(): Collection
    {
        return $this->smallPackages;
    }

    public function addSmallPackage(SmallPackage $smallPackage): static
    {
        if (!$this->smallPackages->contains($smallPackage)) {
            $this->smallPackages->add($smallPackage);
            $smallPackage->setDeliveryMode($this);
        }

        return $this;
    }

    public function removeSmallPackage(SmallPackage $smallPackage): static
    {
        if ($this->smallPackages->removeElement($smallPackage)) {
            // set the owning side to null (unless already changed)
            if ($smallPackage->getDeliveryMode() === $this) {
                $smallPackage->setDeliveryMode(null);
            }
        }

        return $this;
    }
}
