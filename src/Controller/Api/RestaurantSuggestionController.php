<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\RestaurantSuggestionDTO;
use App\Entity\RestaurantSuggestion;
use App\Enum\RestaurantSuggestionStatus;
use App\Repository\CountryRepository;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RestaurantSuggestionController extends AbstractController {

    public function __construct(
        private readonly SerializerInterface    $serializer,
        private readonly ValidatorInterface     $validator,
        private readonly EntityManagerInterface $entityManager,
        private readonly RestaurantRepository   $restaurantRepository,
        private readonly CountryRepository      $countryRepository
    ) {
    }

    #[Route('/api/restaurant-suggestions', name: 'api_restaurant_suggestions', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse {
        $dto = $this->serializer->deserialize($request->getContent(), RestaurantSuggestionDTO::class, 'json');
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return $this->json(['errors' => (string)$errors], 400);
        }

        $restaurant = $dto->restaurantId ? $this->restaurantRepository->find($dto->restaurantId) : null;
        $country = $dto->fields->countryId ? $this->countryRepository->find($dto->fields->countryId) : null;

        $suggestion = new RestaurantSuggestion();
        $suggestion->setRestaurant($restaurant);
        $suggestion->setStatus(RestaurantSuggestionStatus::PENDING);
        $suggestion->setFields([
            'name' => $dto->fields->name,
            'street' => $dto->fields->street,
            'houseNumber' => $dto->fields->houseNumber,
            'postalCode' => $dto->fields->postalCode,
            'city' => $dto->fields->city,
            'countryId' => $country?->getId(),
        ]);

        $this->entityManager->persist($suggestion);
        $this->entityManager->flush();

        return $this->json(['success' => true], 201);
    }
}
