<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RestaurantSuggestionDTO {

    public ?int $restaurantId = null;

    #[Assert\Valid]
    public RestaurantEditDTO $fields;



}
