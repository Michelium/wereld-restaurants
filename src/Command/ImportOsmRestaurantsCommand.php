<?php

namespace App\Command;

use App\Entity\Country;
use App\Entity\Restaurant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-osm-restaurants',
    description: 'Import restaurants from an OSM GeoJSON file',
)]
class ImportOsmRestaurantsCommand extends Command {

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

    public function __construct(private EntityManagerInterface $em) {
        parent::__construct();
    }

    protected function configure(): void {
        $this
            ->addArgument('path', InputArgument::REQUIRED, 'Path to the GeoJSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $path = $input->getArgument('path');

        if (!file_exists($path)) {
            $io->error("File not found: $path");
            return Command::FAILURE;
        }

        $data = json_decode(file_get_contents($path), true);

        if (!isset($data['features'])) {
            $io->error("Invalid GeoJSON structure");
            return Command::FAILURE;
        }

        $created = 0;
        foreach ($data['features'] as $feature) {
            $props = $feature['properties'] ?? [];
            $geometry = $feature['geometry']['coordinates'] ?? null;

            if (!isset($props['name'], $feature['id'], $geometry[0], $geometry[1], $props['addr:postcode'], $props['addr:city'])) {
                continue;
            }

            $osmId = $feature['id'];

            // Skip if already imported
            $existing = $this->em->getRepository(Restaurant::class)->findOneBy(['osmId' => $osmId]);
            if ($existing) {
                continue;
            }

            $io->writeln("Importing restaurant: {$props['name']} (OSM ID: $osmId)");

            $restaurant = new Restaurant();
            $restaurant->setOsmId($osmId);
            $restaurant->setName($props['name']);
            $restaurant->setLatitude($geometry[1]);
            $restaurant->setLongitude($geometry[0]);
            $restaurant->setStreet($props['addr:street'] ?? null);
            $restaurant->setHouseNumber($props['addr:housenumber'] ?? null);

            $postcode = explode(';', $props['addr:postcode'])[0];
            $restaurant->setPostalCode($postcode);

            $restaurant->setCity($props['addr:city']);

            $cuisineRaw = strtolower($props['cuisine'] ?? '');
            $restaurant->setOsmCuisine($cuisineRaw ?: null);
            $matchedCountry = null;
            foreach (explode(';', $cuisineRaw) as $cuisinePart) {
                $normalized = trim($cuisinePart);

                if (isset(self::CUISINE_TO_COUNTRY[$normalized])) {
                    $countryCode = self::CUISINE_TO_COUNTRY[$normalized];
                    $country = $this->em->getRepository(Country::class)->findOneBy(['code' => $countryCode]);

                    if ($country) {
                        $matchedCountry = $country;
                        break;
                    }
                }
            }
            if ($matchedCountry) {
                $restaurant->setCountry($matchedCountry);
            }


            $restaurant->setWebsite($props['website'] ?? null);

            $this->em->persist($restaurant);
            $created++;
        }

        $this->em->flush();
        $io->success("$created restaurant(s) imported.");
        return Command::SUCCESS;
    }
}
