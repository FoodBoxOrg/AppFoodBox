<?php

namespace App\Entity;

use App\Repository\FoodTagsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FoodTagsRepository::class)]
class FoodTags
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Food::class, inversedBy: 'foodTags')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Food $food = null;

    #[ORM\ManyToOne(targetEntity: Tags::class, inversedBy: 'foodTags')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tags $tag = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFood(): ?Food
    {
        return $this->food;
    }

    public function setFood(Food $food): static
    {
        $this->food = $food;
        return $this;
    }

    public function getTag(): ?Tags
    {
        return $this->tag;
    }

    public function setTag(Tags $tag): static
    {
        $this->tag = $tag;
        return $this;
    }
}
