<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RestaurantEditDTO {

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public ?string $name = null;

    #[Assert\Length(max: 255)]
    public ?string $street = null;

    #[Assert\Length(max: 10)]
    public ?string $houseNumber = null;

    #[Assert\Length(max: 10)]
    public ?string $postalCode = null;

    #[Assert\Length(max: 255)]
    public ?string $city = null;

    #[Assert\NotBlank]
    #[Assert\Type("int")]
    public ?int $countryId = null;

}
