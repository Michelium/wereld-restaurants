<?php

namespace App\Service;

use App\DTO\RestaurantSuggestionDTO;
use App\Entity\Restaurant;
use App\Entity\RestaurantSuggestion;
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
            // If the restaurant doesn't exist, we don't create a restaurant, this is done after the suggestion is approved by the user.
            $restaurantSuggestion->setStatus(RestaurantSuggestionStatus::APPROVED);
            $this->entityManager->persist($restaurantSuggestion);
            $this->entityManager->flush();
            return;
        }

        $restaurant = $restaurantSuggestion->isNewRestaurant()
            ? new Restaurant()
            : $restaurantSuggestion->getRestaurant();

        switch ($restaurantSuggestion->getType()) {
            case RestaurantSuggestionType::FIELDS:
                $restaurant->setName($restaurantSuggestion->getFields()['name'] ?? $restaurant->getName());
                $restaurant->setStreet($restaurantSuggestion->getFields()['street'] ?? $restaurant->getStreet());
                $restaurant->setHouseNumber($restaurantSuggestion->getFields()['houseNumber'] ?? $restaurant->getHouseNumber());
                $restaurant->setPostalCode($restaurantSuggestion->getFields()['postalCode'] ?? $restaurant->getPostalCode());
                $restaurant->setCity($restaurantSuggestion->getFields()['city'] ?? $restaurant->getCity());
                $restaurant->setCountry($restaurantSuggestion->getFields()['countryId'] ? $this->countryRepository->find($restaurantSuggestion->getFields()['countryId']) : null);

                break;
            case RestaurantSuggestionType::CLOSED:
                $restaurant->setStatus(RestaurantStatus::CLOSED);
                break;
            default:
                throw new \InvalidArgumentException('Unsupported suggestion type.');
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
}
