<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\RestaurantSuggestionDTO;
use App\Enum\RestaurantSuggestionType;
use App\Service\RestaurantSuggestionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RestaurantSuggestionController extends AbstractController {

    public function __construct(
        private readonly SerializerInterface         $serializer,
        private readonly ValidatorInterface          $validator,
        private readonly EntityManagerInterface      $entityManager,
        private readonly RestaurantSuggestionService $restaurantSuggestionService
    ) {
    }

    #[Route('/api/restaurant-suggestions', name: 'api_restaurant_suggestions', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), RestaurantSuggestionDTO::class, 'json');
        } catch (ExceptionInterface $e) {
            return $this->json(['error' => 'Invalid JSON format: ' . $e->getMessage()], 400);
        }

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return $this->json(['errors' => (string)$errors], 400);
        }

        $restaurantSuggestion = $dto->type === RestaurantSuggestionType::CLOSED->value
            ? $this->restaurantSuggestionService->createCloseSuggestion($dto)
            : $this->restaurantSuggestionService->createFromDTO($dto);

        $this->entityManager->persist($restaurantSuggestion);
        $this->entityManager->flush();

        return $this->json(['success' => true], 201);
    }
}
