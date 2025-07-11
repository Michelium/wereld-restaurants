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

        if (!empty($countryCodes)) {
            $restaurants = $this->restaurantRepository->findByCountries($countryCodes);
        } else {
            $restaurants = $this->restaurantRepository->findAllWithCountry();
        }

        return $this->json($restaurants, 200, [], ['groups' => ['restaurant:read']]);
    }
}
