<?php

namespace App\Entity;

use App\Entity\Trait\EntityLifecycleTrait;
use App\Enum\RestaurantSuggestionStatus;
use App\Repository\RestaurantSuggestionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: RestaurantSuggestionRepository::class)]
class RestaurantSuggestion {

    use EntityLifecycleTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['restaurant_suggestion:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['restaurant_suggestion:read'])]
    private array $fields = [];

    #[ORM\ManyToOne(inversedBy: 'restaurantSuggestions')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Groups(['restaurant_suggestion:read'])]
    private ?Restaurant $restaurant = null;

    #[ORM\Column(type: 'string', enumType: RestaurantSuggestionStatus::class)]
    private RestaurantSuggestionStatus $status = RestaurantSuggestionStatus::PENDING;

    public function getId(): ?int {
        return $this->id;
    }

    public function getFields(): array {
        return $this->fields;
    }

    public function setFields(array $fields): static {
        $this->fields = $fields;

        return $this;
    }

    public function getRestaurant(): ?Restaurant {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): static {
        $this->restaurant = $restaurant;

        return $this;
    }

    public function getStatus(): RestaurantSuggestionStatus {
        return $this->status;
    }

    public function setStatus(RestaurantSuggestionStatus $status): static {
        $this->status = $status;
        return $this;
    }

}
