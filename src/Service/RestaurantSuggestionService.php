<?php

namespace App\Service;

use App\DTO\RestaurantSuggestionDTO;
use App\Entity\Restaurant;
use App\Entity\RestaurantSuggestion;
use App\Enum\RestaurantFieldSource;
use App\Enum\RestaurantStatus;
use App\Enum\RestaurantSuggestionStatus;
use App\Enum\RestaurantSuggestionType;
use App\Repository\CountryRepository;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly final class RestaurantSuggestionService {

    public function __construct(
        private EntityManagerInterface $entityManager,
        private CountryRepository      $countryRepository,
        private RestaurantRepository   $restaurantRepository,
    ) {
    }

    public function approveSuggestion(RestaurantSuggestion $restaurantSuggestion): void {
        if ($restaurantSuggestion->getType() === RestaurantSuggestionType::NEW) {
            // If the restaurant doesn't exist, we don't create a restaurant, this is done after the suggestion is approved by the admin.
            $restaurantSuggestion->setStatus(RestaurantSuggestionStatus::APPROVED);
            $this->entityManager->persist($restaurantSuggestion);
            $this->entityManager->flush();
            return;
        }

        $restaurant = $restaurantSuggestion->isNewRestaurant()
            ? new Restaurant()
            : $restaurantSuggestion->getRestaurant();

        if (!$restaurant) {
            throw new \InvalidArgumentException('Restaurant not found for suggestion.');
        }

        $fields = $restaurantSuggestion->getFields();
        $changed = [];

        switch ($restaurantSuggestion->getType()) {
            case RestaurantSuggestionType::FIELDS:
                $this->applyIfChanged($restaurant, 'name', $fields['name'] ?? null, $changed);
                $this->applyIfChanged($restaurant, 'street', $fields['street'] ?? null, $changed);
                $this->applyIfChanged($restaurant, 'houseNumber', $fields['houseNumber'] ?? null, $changed);
                $this->applyIfChanged($restaurant, 'postalCode', $fields['postalCode'] ?? null, $changed);
                $this->applyIfChanged($restaurant, 'city', $fields['city'] ?? null, $changed);
                $this->applyIfChanged($restaurant, 'website', $fields['website'] ?? null, $changed);

                // association: country
                if (array_key_exists('countryId', $fields)) {
                    $newCountry = $fields['countryId'] ? $this->countryRepository->find($fields['countryId']) : null;
                    $currentCountry = $restaurant->getCountry();
                    if (($currentCountry?->getId()) !== ($newCountry?->getId())) {
                        $restaurant->setCountry($newCountry);
                        $changed[] = 'country';
                    }
                }

                break;
            case RestaurantSuggestionType::CLOSED:
                if ($restaurant->getStatus() !== RestaurantStatus::CLOSED) {
                    $restaurant->setStatus(RestaurantStatus::CLOSED);
                    $changed[] = 'status';
                }
                break;
            default:
                throw new \InvalidArgumentException('Unsupported suggestion type.');
        }

        // If this suggestion created a new Restaurant instance here, then all provided fields are considered user-sourced.
        // Otherwise, only mark *changed* fields as user-sourced.
        if (method_exists($restaurant, 'markFields')) {
            $now = new \DateTimeImmutable();
            if ($restaurantSuggestion->isNewRestaurant()) {
                $toMark = ['name', 'street', 'houseNumber', 'postalCode', 'city', 'website', 'country'];
                $restaurant->setFieldSources($toMark, $now, RestaurantFieldSource::USER);
            } else {
                if ($changed !== []) {
                    $restaurant->setFieldSources($changed, $now, RestaurantFieldSource::USER);
                }
            }
        }

        $this->entityManager->persist($restaurant);

        $restaurantSuggestion->setStatus(RestaurantSuggestionStatus::APPROVED);
        $this->entityManager->persist($restaurantSuggestion);

        $this->entityManager->flush();
    }

    public function rejectSuggestion(RestaurantSuggestion $restaurantSuggestion): void {
        $restaurantSuggestion->setStatus(RestaurantSuggestionStatus::REJECTED);

        $this->entityManager->persist($restaurantSuggestion);
        $this->entityManager->flush();
    }

    public function createFromDTO(RestaurantSuggestionDTO $dto): RestaurantSuggestion {
        $restaurant = $this->getRestaurantFromDTO($dto);
        $country = $dto->fields->countryId ? $this->countryRepository->find($dto->fields->countryId) : null;

        $restaurantSuggestion = new RestaurantSuggestion();
        $restaurantSuggestion->setRestaurant($restaurant);
        $restaurantSuggestion->setStatus(RestaurantSuggestionStatus::PENDING);
        $restaurantSuggestion->setType($dto->getTypeAsEnum());
        $restaurantSuggestion->setComment($dto->comment);
        $restaurantSuggestion->setNewRestaurant($dto->newRestaurant);
        $restaurantSuggestion->setFields([
            'name' => $dto->fields->name,
            'street' => $dto->fields->street,
            'houseNumber' => $dto->fields->houseNumber,
            'postalCode' => $dto->fields->postalCode,
            'city' => $dto->fields->city,
            'countryId' => $country?->getId(),
            'website' => $dto->fields->website,
        ]);

        return $restaurantSuggestion;
    }

    public function createCloseSuggestion(RestaurantSuggestionDTO $dto): RestaurantSuggestion {
        $restaurant = $this->getRestaurantFromDTO($dto);

        if (!$restaurant) {
            throw new \InvalidArgumentException('Restaurant not found for closed suggestion.');
        }

        $restaurantSuggestion = new RestaurantSuggestion();
        $restaurantSuggestion->setRestaurant($restaurant);
        $restaurantSuggestion->setStatus(RestaurantSuggestionStatus::PENDING);
        $restaurantSuggestion->setType(RestaurantSuggestionType::CLOSED);

        return $restaurantSuggestion;
    }

    private function getRestaurantFromDTO(RestaurantSuggestionDTO $dto): ?Restaurant {
        if ($dto->restaurantId) {
            return $this->restaurantRepository->find($dto->restaurantId);
        }
        return null;

    }

    /**
     * Apply the new value to the entity if it has changed, and record the field name in the changed array.
     *
     * If $new !== current, set it and add $field to $changed.
     */
    private function applyIfChanged(Restaurant $restaurant, string $field, mixed $new, array &$changed): void {
        $getter = 'get' . ucfirst($field);
        $setter = 'set' . ucfirst($field);
        if (!method_exists($restaurant, $getter) || !method_exists($restaurant, $setter)) {
            return;
        }

        $current = $restaurant->$getter();

        // normalise strings to avoid marking "same but with spaces" as a change
        if (is_string($current) || is_string($new)) {
            $current = $this->normaliseString($current);
            $new = $this->normaliseString($new);
        }

        if ($current !== $new) {
            $restaurant->$setter($new);
            $changed[] = $field;
        }
    }

    private function normaliseString(?string $value): ?string {
        if ($value === null) return null;
        $value = trim($value);
        return $value === '' ? null : $value;
    }
}
