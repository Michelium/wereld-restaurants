<?php

namespace App\Service;

use App\Entity\Country;
use App\Entity\Restaurant;
use App\Enum\RestaurantFieldSource;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OsmRestaurantImporter {

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly HttpClientInterface    $httpClient,
        private readonly LoggerInterface        $logger,
    ) {
    }

    public function importFromOverpass(): array {
        $query = <<<OVERPASS
[out:json][timeout:60];
area["name"="Nederland"]->.searchArea;

(
  node["amenity"="restaurant"]["cuisine"~"^(afghan|algerian|argentinian|armenian|bangladeshi|belgian|brazilian|british|bulgarian|burmese|cambodian|canadian|chinese|croatian|cuban|czech|danish|dutch|egyptian|ethiopian|filipino|finnish|french|georgian|german|greek|hungarian|indian|indonesian|iranian|iraqi|irish|israeli|italian|jamaican|japanese|korean|kurdish|laotian|lebanese|malaysian|mexican|moroccan|nepalese|nigerian|norwegian|pakistani|peruvian|polish|portuguese|romanian|russian|serbian|singaporean|somali|spanish|sri_lankan|swedish|syrian|taiwanese|thai|tunisian|turkish|ukrainian|vietnamese|yemeni)$"](area.searchArea);
  way["amenity"="restaurant"]["cuisine"~"^(afghan|algerian|argentinian|armenian|bangladeshi|belgian|brazilian|british|bulgarian|burmese|cambodian|canadian|chinese|croatian|cuban|czech|danish|dutch|egyptian|ethiopian|filipino|finnish|french|georgian|german|greek|hungarian|indian|indonesian|iranian|iraqi|irish|israeli|italian|jamaican|japanese|korean|kurdish|laotian|lebanese|malaysian|mexican|moroccan|nepalese|nigerian|norwegian|pakistani|peruvian|polish|portuguese|romanian|russian|serbian|singaporean|somali|spanish|sri_lankan|swedish|syrian|taiwanese|thai|tunisian|turkish|ukrainian|vietnamese|yemeni)$"](area.searchArea);
  relation["amenity"="restaurant"]["cuisine"~"^(afghan|algerian|argentinian|armenian|bangladeshi|belgian|brazilian|british|bulgarian|burmese|cambodian|canadian|chinese|croatian|cuban|czech|danish|dutch|egyptian|ethiopian|filipino|finnish|french|georgian|german|greek|hungarian|indian|indonesian|iranian|iraqi|irish|israeli|italian|jamaican|japanese|korean|kurdish|laotian|lebanese|malaysian|mexican|moroccan|nepalese|nigerian|norwegian|pakistani|peruvian|polish|portuguese|romanian|russian|serbian|singaporean|somali|spanish|sri_lankan|swedish|syrian|taiwanese|thai|tunisian|turkish|ukrainian|vietnamese|yemeni)$"](area.searchArea);
);
out center;
OVERPASS;

        $response = $this->httpClient->request('POST', 'https://overpass-api.de/api/interpreter', [
            'body' => ['data' => $query],
        ]);
        $this->logger->info("Overpass API response status: " . $response->getStatusCode());

        $elements = json_decode($response->getContent(), true)['elements'] ?? [];
        $this->logger->info("Overpass API returned " . count($elements) . " elements.");

        return array_map(function ($el) {
            // normalise lat/lng from centre if way/relation
            $lat = $el['lat'] ?? $el['center']['lat'] ?? null;
            $lon = $el['lon'] ?? $el['center']['lon'] ?? null;

            return [
                'id' => $el['type'] . '/' . $el['id'],
                'geometry' => ['type' => 'Point', 'coordinates' => [$lon, $lat]],
                'properties' => $el['tags'] ?? [],
            ];
        }, array_filter($elements, fn($el) => isset($el['tags']['cuisine'], $el['lat']) || isset($el['center'])));
    }


    /**
     * The .geojson file can be downloaded from:
     * https://overpass-turbo.eu/
     *
     * Use the same query as in importFromOverpass()
     * @param string $path
     * @return array
     */
    public function importFromFile(string $path): array {
        $json = file_get_contents($path);
        $data = json_decode($json, true);

        if (!isset($data['features'])) {
            throw new \RuntimeException("Invalid GeoJSON structure.");
        }

        $this->logger->info("GeoJSON file contains " . count($data['features']) . " features.");

        return $data['features'];
    }

    /**
     * @return int Number of restaurants created or updated
     */
    public function importFeatures(array $features): int {
        $createdOrUpdated = 0;
        $now = new \DateTimeImmutable();

        foreach ($features as $feature) {
            $props = $feature['properties'] ?? [];
            $coords = $feature['geometry']['coordinates'] ?? null;

            if (!isset($props['name'], $feature['id'], $coords[0], $coords[1])) {
                continue;
            }

            $osmId = $feature['id'];

            $restaurant = $this->entityManager->getRepository(Restaurant::class)->findOneBy(['osmId' => $osmId]);

            if (!$restaurant) {
                $restaurant = new Restaurant();
                $restaurant->setOsmId($osmId);
            }

            // Field update logic (only if new or existing source = OSM or empty)
            $this->maybeUpdate($restaurant, 'name', $props['name'], $now);
            $this->maybeUpdate($restaurant, 'latitude', $coords[1], $now);
            $this->maybeUpdate($restaurant, 'longitude', $coords[0], $now);
            $this->maybeUpdate($restaurant, 'street', $props['addr:street'] ?? null, $now);
            $this->maybeUpdate($restaurant, 'houseNumber', $props['addr:housenumber'] ?? null, $now);
            $this->maybeUpdate($restaurant, 'postalCode', explode(';', $props['addr:postcode'] ?? '')[0] ?? null, $now);
            $this->maybeUpdate($restaurant, 'city', $props['addr:city'] ?? null, $now);
            $this->maybeUpdate($restaurant, 'website', $props['website'] ?? null, $now);

            // Set cuisine and try to assign country
            $cuisineRaw = strtolower($props['cuisine'] ?? '');
            $restaurant->setOsmCuisine($cuisineRaw ?: null);

            foreach (explode(';', $cuisineRaw) as $cuisinePart) {
                $code = $this->mapCuisineToCountry(trim($cuisinePart));
                if ($code) {
                    $country = $this->entityManager->getRepository(Country::class)->findOneBy(['code' => $code]);
                    if ($country && (!$restaurant->getCountry() || $restaurant->getFieldSource('country') === 'osm')) {
                        $restaurant->setCountry($country);
                        $restaurant->setFieldSource('country', $now, RestaurantFieldSource::OSM);
                    }
                    break;
                }
            }

            $this->entityManager->persist($restaurant);
            $createdOrUpdated++;
        }

        $this->entityManager->flush();
        return $createdOrUpdated;
    }

    private function maybeUpdate(Restaurant $restaurant, string $field, mixed $value, \DateTimeInterface $now): void {
        if ($value === null) {
            return;
        }

        $getter = 'get' . ucfirst($field);
        $setter = 'set' . ucfirst($field);
        $currentSource = $restaurant->getFieldSource($field);

        if ($restaurant->$getter() !== null && $currentSource !== RestaurantFieldSource::OSM->value && $currentSource !== null) {
            return;
        }

        $restaurant->$setter($value);
        $restaurant->setFieldSource($field, $now, RestaurantFieldSource::OSM);
    }

    private function mapCuisineToCountry(string $cuisine): ?string {
        return self::CUISINE_TO_COUNTRY[$cuisine] ?? null;
    }

    private const CUISINE_TO_COUNTRY = [
        'afghan' => 'AF',
        'algerian' => 'DZ',
        'argentinian' => 'AR',
        'armenian' => 'AM',
        'australian' => 'AU',
        'austrian' => 'AT',
        'azerbaijani' => 'AZ',
        'bangladeshi' => 'BD',
        'belgian' => 'BE',
        'brazilian' => 'BR',
        'british' => 'GB',
        'bulgarian' => 'BG',
        'burmese' => 'MM',
        'cambodian' => 'KH',
        'canadian' => 'CA',
        'chilean' => 'CL',
        'chinese' => 'CN',
        'colombian' => 'CO',
        'croatian' => 'HR',
        'cuban' => 'CU',
        'czech' => 'CZ',
        'danish' => 'DK',
        'dutch' => 'NL',
        'egyptian' => 'EG',
        'english' => 'GB',
        'estonian' => 'EE',
        'ethiopian' => 'ET',
        'filipino' => 'PH',
        'finnish' => 'FI',
        'french' => 'FR',
        'georgian' => 'GE',
        'german' => 'DE',
        'greek' => 'GR',
        'guatemalan' => 'GT',
        'hungarian' => 'HU',
        'icelandic' => 'IS',
        'indian' => 'IN',
        'indonesian' => 'ID',
        'iranian' => 'IR',
        'iraqi' => 'IQ',
        'irish' => 'IE',
        'israeli' => 'IL',
        'italian' => 'IT',
        'jamaican' => 'JM',
        'japanese' => 'JP',
        'jordanian' => 'JO',
        'kazakh' => 'KZ',
        'kenyan' => 'KE',
        'korean' => 'KR',
        'kurdish' => 'IQ', // no official ISO country, closest would be Iraq/Iran/Syria
        'laotian' => 'LA',
        'latvian' => 'LV',
        'lebanese' => 'LB',
        'libyan' => 'LY',
        'lithuanian' => 'LT',
        'macedonian' => 'MK',
        'malaysian' => 'MY',
        'mexican' => 'MX',
        'mongolian' => 'MN',
        'moroccan' => 'MA',
        'nepalese' => 'NP',
        'new_zealand' => 'NZ',
        'nigerian' => 'NG',
        'norwegian' => 'NO',
        'pakistani' => 'PK',
        'palestinian' => 'PS',
        'peruvian' => 'PE',
        'philippine' => 'PH',
        'polish' => 'PL',
        'portuguese' => 'PT',
        'romanian' => 'RO',
        'russian' => 'RU',
        'scottish' => 'GB',
        'serbian' => 'RS',
        'singaporean' => 'SG',
        'slovak' => 'SK',
        'slovenian' => 'SI',
        'somali' => 'SO',
        'south_african' => 'ZA',
        'spanish' => 'ES',
        'sri_lankan' => 'LK',
        'swedish' => 'SE',
        'swiss' => 'CH',
        'syrian' => 'SY',
        'taiwanese' => 'TW',
        'tamil' => 'IN',
        'thai' => 'TH',
        'tibetan' => 'CN',
        'tunisian' => 'TN',
        'turkish' => 'TR',
        'ukrainian' => 'UA',
        'uruguayan' => 'UY',
        'uzbek' => 'UZ',
        'venezuelan' => 'VE',
        'vietnamese' => 'VN',
        'welsh' => 'GB',
        'yemeni' => 'YE',
        'bosnian' => 'BA',
        'albanian' => 'AL',
        'belarusian' => 'BY',
        'andorran' => 'AD',
        'bahaman' => 'BS',
        'bahraini' => 'BH',
        'barbadian' => 'BB',
        'bruneian' => 'BN',
        'central_african' => 'CF',
        'chadian' => 'TD',
        'congolese' => 'CD', // or CG for Republic
        'djiboutian' => 'DJ',
        'dominican' => 'DO',
        'ecuadorian' => 'EC',
        'elsalvadorian' => 'SV',
        'eritrean' => 'ER',
        'gabonese' => 'GA',
        'gambian' => 'GM',
        'ghanaian' => 'GH',
        'guinean' => 'GN',
        'honduran' => 'HN',
        'kuwaiti' => 'KW',
        'liberian' => 'LR',
        'maldivian' => 'MV',
        'maltese' => 'MT',
        'moldovan' => 'MD',
        'mozambican' => 'MZ',
        'nicaraguan' => 'NI',
        'omani' => 'OM',
        'panamanian' => 'PA',
        'paraguayan' => 'PY',
        'qatari' => 'QA',
        'rwandan' => 'RW',
        'saudi' => 'SA',
        'senegalese' => 'SN',
        'sudanese' => 'SD',
        'tanzanian' => 'TZ',
        'trinidadian' => 'TT',
        'ugandan' => 'UG',
        'zambian' => 'ZM',
        'zimbabwean' => 'ZW',

        'italian_pizza' => 'IT',
        'pizza' => 'IT',
        'sushi' => 'JP',
        'kebab' => 'TR',
        'doner' => 'TR',
        'tapas' => 'ES',
        'bbq' => 'US',
        'burrito' => 'MX',
        'gyros' => 'GR',
        'shawarma' => 'IL',
        'falafel' => 'IL',
        'paella' => 'ES',
        'pho' => 'VN',
        'tandoori' => 'IN',
    ];
}
