<?php

namespace App\Entity;

use App\Repository\FoodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FoodRepository::class)]
class Food
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private ?string $name = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $origin = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageId = null;

    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'food', cascade: ['persist', 'remove'])]
    private Collection $reviews;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(?string $origin): static
    {
        $this->origin = $origin;
        return $this;
    }

    public function getImageId(): ?int
    {
        return $this->imageId;
    }

    public function setImageId(?int $imageId): static
    {
        $this->imageId = $imageId;
        return $this;
    }

    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function getComment(): ArrayCollection
    {
        return $this->reviews->map(fn(Review $review) => $review->getComment());
    }

    public function getAverageRating(): ?float
    {
        $reviews = $this->getReviews();

        if ($reviews->isEmpty()) {
            return null;
        }

        $sum = array_reduce($reviews->toArray(), fn($carry, Review $review) => $carry + $review->getRate(), 0);

        return round($sum / count($reviews), 1);
    }
}
