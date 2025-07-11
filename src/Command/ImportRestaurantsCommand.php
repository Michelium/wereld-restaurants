<?php

namespace App\Command;

use App\Entity\Restaurant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-restaurants',
    description: 'Imports restaurants from .csv file, created from .qgz file using QGIS.',
)]
class ImportRestaurantsCommand extends Command {

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('file');

        if (!file_exists($filePath)) {
            $io->error('File not found');
            return Command::FAILURE;
        }

        $data = $this->transformCsvToArray($filePath, $io);

        if (empty($data)) {
            $io->error('No data found in the file');
            return Command::FAILURE;
        }

        foreach ($data as $key => $row) {
            if ($key === 0) continue; // Skip header row

            if (count($row) < 10) {
                $io->writeln("Skipping row $key: Not enough columns");
                continue;
            }

            if (empty($row[6] || empty($row[9] || empty($row[10])))) {
                $io->writeln("Skipping row $key: Missing required data");
                continue;
            }

            $restaurant = new Restaurant();

            $restaurant->setName($row[6]);
            $restaurant->setLatitude((float)$row[9]);
            $restaurant->setLongitude((float)$row[8]);
            $restaurant->setCountryCode(!empty($row[7]) ? $row[7] : null);
            $restaurant->setStreet(!empty($row[5]) ? $row[5] : null);
            $restaurant->setPostalCode(!empty($row[4]) ? $row[4] : null);
            $restaurant->setCity(!empty($row[2]) ? $row[2] : null);
            $restaurant->setHouseNumber(!empty($row[3]) ? $row[3] : null);

            $this->entityManager->persist($restaurant);
        }

        $this->entityManager->flush();

        $io->success('All restaurants imported successfully.');

        return Command::SUCCESS;
    }

    public function transformCsvToArray(string $filePath, SymfonyStyle $io): array {
        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        } else {
            $io->error('Could not open file for reading');
        }
        return $rows;
    }
}
