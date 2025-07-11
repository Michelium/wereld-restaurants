<?php

namespace App\Controller\Api;

use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/countries', name: 'api_country')]
class CountryController extends AbstractController {

    public function __construct(
        private readonly CountryRepository $countryRepository,
    ) {
    }

    #[Route('', name: '_all', methods: ['GET'])]
    public function all(): JsonResponse {
        $restaurants = $this->countryRepository->findBy([], orderBy: ['name' => 'ASC']);

        return $this->json($restaurants, 200, [], ['groups' => ['country:read']]);
    }

}
