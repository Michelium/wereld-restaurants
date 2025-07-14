<?php

namespace App\Controller\Api;

use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/restaurants', name: 'api_restaurant')]
class RestaurantController extends AbstractController {

    public function __construct(
        private readonly RestaurantRepository $restaurantRepository,
    ) {
    }

    #[Route('', name: '_all', methods: ['GET'])]
    public function all(Request $request): JsonResponse {
        $countryCodes = $request->query->all('countries');
        $bounds = $request->query->get('bounds');

        if ($bounds) {
            $decoded = json_decode($bounds, true);
            if (is_array($decoded)) {
                $restaurants = $this->restaurantRepository->findWithinBounds($decoded, $countryCodes);
            } else {
                return $this->json(['error' => 'Invalid bounds'], 400);
            }
        } else {
            $restaurants = $this->restaurantRepository->findAllWithCountry();
        }

        return $this->json($restaurants, 200, [], ['groups' => ['restaurant:read']]);
    }
}
