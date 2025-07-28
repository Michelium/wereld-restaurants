<?php

namespace App\Service;

use App\Entity\Restaurant;
use App\Entity\RestaurantSuggestion;
use App\Enum\RestaurantSuggestionStatus;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly final class RestaurantSuggestionService {

    public function __construct(
        private EntityManagerInterface $entityManager,
        private CountryRepository      $countryRepository,
    ) {
    }

    public function approveSuggestion(RestaurantSuggestion $restaurantSuggestion): void {
        $restaurantSuggestion->setStatus(RestaurantSuggestionStatus::APPROVED);

        // If the suggestion is for a new restaurant, we might want to create a new Restaurant entity here.
        $restaurant = $restaurantSuggestion->isNewRestaurant()
            ? new Restaurant()
            : $restaurantSuggestion->getRestaurant();

        $restaurant->setName($restaurantSuggestion->getFields()['name'] ?? $restaurant->getName());
        $restaurant->setStreet($restaurantSuggestion->getFields()['street'] ?? $restaurant->getStreet());
        $restaurant->setHouseNumber($restaurantSuggestion->getFields()['houseNumber'] ?? $restaurant->getHouseNumber());
        $restaurant->setPostalCode($restaurantSuggestion->getFields()['postalCode'] ?? $restaurant->getPostalCode());
        $restaurant->setCity($restaurantSuggestion->getFields()['city'] ?? $restaurant->getCity());
        $restaurant->setCountry($restaurantSuggestion->getFields()['countryId'] ? $this->countryRepository->find($restaurantSuggestion->getFields()['countryId']) : null);

        $this->entityManager->persist($restaurantSuggestion);
        $this->entityManager->persist($restaurant);
        $this->entityManager->flush();
    }

    public function rejectSuggestion(RestaurantSuggestion $restaurantSuggestion): void {
        $restaurantSuggestion->setStatus(RestaurantSuggestionStatus::REJECTED);

        $this->entityManager->persist($restaurantSuggestion);
        $this->entityManager->flush();
    }

}
