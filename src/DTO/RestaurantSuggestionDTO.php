<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RestaurantSuggestionDTO {

    public ?int $restaurantId = null;

    public ?string $comment = null;

    public ?bool $newRestaurant = false;

    #[Assert\Valid]
    public RestaurantEditDTO $fields;



}
