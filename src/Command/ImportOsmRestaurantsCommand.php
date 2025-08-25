<?php

namespace App\Command;

use App\Service\OsmRestaurantImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-osm-restaurants',
    description: 'Import restaurants from an OSM GeoJSON file',
)]
class ImportOsmRestaurantsCommand extends Command {

    public function __construct(
        private readonly OsmRestaurantImporter $osmRestaurantImporter,
    ) {
        parent::__construct();
    }

    protected function configure(): void {
        $this
            ->addArgument('path', InputArgument::OPTIONAL, 'Path to GeoJSON')
            ->addOption('overpass', null, InputOption::VALUE_NONE, 'Use Overpass API');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $useOverpass = $input->getOption('overpass');

        try {
            $features = $useOverpass
                ? $this->osmRestaurantImporter->importFromOverpass()
                : $this->osmRestaurantImporter->importFromFile($input->getArgument('path'));

            $count = $this->osmRestaurantImporter->importFeatures($features);
            $io->success("$count restaurant(s) imported/updated.");
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error("Import failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
