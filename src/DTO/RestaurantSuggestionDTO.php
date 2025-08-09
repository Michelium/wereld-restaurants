<?php

namespace App\DTO;

use App\Enum\RestaurantSuggestionType;
use Symfony\Component\Validator\Constraints as Assert;

class RestaurantSuggestionDTO {

    public ?int $restaurantId = null;

    public ?string $comment = null;

    public ?bool $newRestaurant = false;

    #[Assert\Choice(choices: ['fields', 'closed', 'new'], message: 'Invalid suggestion type.')]
    public ?string $type = null;

    #[Assert\Valid]
    public RestaurantEditDTO $fields;

    public function getTypeAsEnum(): ?RestaurantSuggestionType {
        return $this->type !== null
            ? RestaurantSuggestionType::tryFrom($this->type)
            : null;
    }


}
