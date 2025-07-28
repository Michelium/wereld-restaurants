<?php

namespace App\Controller\Api;

use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/countries', name: 'api_country')]
class CountryController extends AbstractController {

    public function __construct(
        private readonly CountryRepository $countryRepository,
    ) {
    }

    #[Route('', name: '_all', methods: ['GET'])]
    public function all(): JsonResponse {
        $countries = $this->countryRepository->findBy([], orderBy: ['name' => 'ASC']);

        $data = array_map(fn($country) => [
            'id' => $country->getId(),
            'code' => $country->getCode(),
            'name' => $country->getName(),
            'flag' => $country->getFlag(),
            'restaurantCount' => $country->getRestaurants()->count(),
        ], $countries);


        return $this->json($data);
    }

}
