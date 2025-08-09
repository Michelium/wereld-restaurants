<?php

namespace App\DTO;

use App\Entity\Restaurant;
use App\Repository\CountryRepository;
use Symfony\Component\HttpFoundation\Request;

class RestaurantPrefillDTO {

    public ?string $name = null;
    public ?string $street = null;
    public ?string $houseNumber = null;
    public ?string $postalCode = null;
    public ?string $city = null;
    public ?int $countryId = null;

    /**
     * Create from request query params
     * @param Request $request
     * @return self
     */
    public static function fromRequest(Request $request): self {
        $dto = new self();
        $dto->name = $request->query->get('name');
        $dto->street = $request->query->get('street');
        $dto->houseNumber = $request->query->get('houseNumber');
        $dto->postalCode = $request->query->get('postalCode');
        $dto->city = $request->query->get('city');
        $countryId = $request->query->get('countryId');
        $dto->countryId = $countryId !== null ? (int)$countryId : null;

        return $dto;
    }

    /**
     * Create from a plain array (e.g. $suggestion->getFields())
     * @param array $fields
     * @return self
     */
    public static function fromFields(array $fields): self {
        $dto = new self();
        $dto->name = $fields['name'] ?? null;
        $dto->street = $fields['street'] ?? null;
        $dto->houseNumber = $fields['houseNumber'] ?? null;
        $dto->postalCode = $fields['postalCode'] ?? null;
        $dto->city = $fields['city'] ?? null;
        $dto->countryId = isset($fields['countryId']) ? (int)$fields['countryId'] : null;

        return $dto;
    }

    /**
     * Convert this DTO to an array representation
     * This is useful for URL params or API responses.
     *
     * @return array
     */
    public function toArray(): array {
        return [
            'name' => $this->name,
            'street' => $this->street,
            'houseNumber' => $this->houseNumber,
            'postalCode' => $this->postalCode,
            'city' => $this->city,
            'countryId' => $this->countryId,
        ];
    }

    /**
     * Apply this DTO to a Restaurant entity
     * @param Restaurant $restaurant
     * @param CountryRepository $countryRepository
     * @return void
     */
    public function applyTo(Restaurant $restaurant, CountryRepository $countryRepository): void {
        $restaurant->setName($this->name);
        $restaurant->setStreet($this->street);
        $restaurant->setHouseNumber($this->houseNumber);
        $restaurant->setPostalCode($this->postalCode);
        $restaurant->setCity($this->city);

        if ($this->countryId) {
            $country = $countryRepository->find($this->countryId);
            $restaurant->setCountry($country);
        }
    }

    /**
     * Check if any field has a value
     * This is useful to determine if the DTO has meaningful data.
     *
     * @return bool
     */
    public function hasAnyValue(): bool {
        return $this->name || $this->street || $this->houseNumber || $this->postalCode || $this->city || $this->countryId;
    }
}
