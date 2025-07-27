<?php

namespace App\Service;

use App\Entity\RestaurantSuggestion;
use App\Enum\RestaurantSuggestionStatus;
use Doctrine\ORM\EntityManagerInterface;

readonly final class RestaurantSuggestionService {

    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function approveSuggestion(RestaurantSuggestion $restaurantSuggestion): void {
        $restaurantSuggestion->setStatus(RestaurantSuggestionStatus::APPROVED);

        $this->entityManager->persist($restaurantSuggestion);
        $this->entityManager->flush();
    }

    public function rejectSuggestion(RestaurantSuggestion $restaurantSuggestion): void {
        $restaurantSuggestion->setStatus(RestaurantSuggestionStatus::REJECTED);

        $this->entityManager->persist($restaurantSuggestion);
        $this->entityManager->flush();
    }

}
