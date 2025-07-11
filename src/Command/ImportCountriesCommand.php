<?php

namespace App\Command;

use App\Entity\Country;
use App\Entity\Restaurant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(
    name: 'app:import:countries',
    description: 'Imports countries from countries.nl.yaml into the database.',
)]
class ImportCountriesCommand extends Command {
    private string $flagsPath = __DIR__ . '/../../assets/images/flags'; // adjust if needed
    private string $yamlPath = __DIR__ . '/../../translations/countries.nl.yaml';

    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
         $restaurants = $this->em->getRepository(Restaurant::class)->findAll();
        $updatedCount = 0;

        foreach ($restaurants as $restaurant) {
            $code = strtoupper($restaurant->getCountryCode() ?? '');
            $country = $this->em->getRepository(Country::class)->findOneBy(['code' => $code]);

            if ($country) {
                $restaurant->setCountry($country);
                $this->em->persist($restaurant);
                $updatedCount++;
                continue;
            }
        }

        $this->em->flush();

        $output->writeln('<info>Linked ' . $updatedCount . ' restaurants to countries.</info>');

        return Command::SUCCESS;
    }
}
