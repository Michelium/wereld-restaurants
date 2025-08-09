<?php

namespace App;

use App\Entity\Restaurant;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeocodingService {

    private const NOMINATIM_API_URL = 'https://nominatim.openstreetmap.org/search';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface     $logger,
    ) {
    }

    /**
     * Geocode an address using the Nominatim API.
     * This method sends a request to the Nominatim API to retrieve geocoding information
     * for the provided address. It expects the address to be a string and returns
     * the geocoded data as an associative array.
     * @param string $address
     * @return array|null
     */
    #[ArrayShape(['lat' => 'float', 'lon' => 'float'])]
    public function geocode(string $address): ?array {
        try {
            $response = $this->httpClient->request('GET', self::NOMINATIM_API_URL, [
                'query' => [
                    'q' => $address,
                    'format' => 'json',
                    'addressdetails' => 1,
                    'limit' => 1,
                ],
                'headers' => [
                    'User-Agent' => 'wereld-restaurants/1.0',
                ],
            ]);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Geocoding request failed', [
                'address' => $address,
                'error' => $e->getMessage(),
            ]);
            return null;
        }

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $data = $response->toArray();

        if (empty($data)) {
            return null;
        }

        if (isset($data[0]['lat'], $data[0]['lon'])) {
            return [
                'lat' => (float)$data[0]['lat'],
                'lon' => (float)$data[0]['lon'],
            ];
        }

        return null;
    }

    /**
     * Geocode an address based on a Restaurant entity.
     * This method constructs an address from the properties of a Restaurant entity
     * @param Restaurant $restaurant
     * @return array|null
     */
    #[ArrayShape(['lat' => 'float', 'lon' => 'float'])]
    public function geocodeFromRestaurant(Restaurant $restaurant): ?array {
        $addressParts = [
            $restaurant->getStreet(),
            $restaurant->getHouseNumber(),
            $restaurant->getPostalCode(),
            $restaurant->getCity(),
        ];

        // Filter out any null or empty parts
        $addressParts = array_filter($addressParts, fn($part) => !empty($part));

        if (empty($addressParts)) {
            return null;
        }

        // Join the address parts into a single string
        $address = implode(', ', $addressParts);

        return $this->geocode($address);

    }
}
