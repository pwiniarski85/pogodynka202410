<?php

namespace App\Command;

use App\Repository\LocationRepository;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
#[AsCommand(
    name: 'weather:location',
    description: 'Add a short description for your command',
)]
class WeatherLocationCommand extends Command
{
    public function __construct(private readonly WeatherUtil $util, private readonly LocationRepository $locationRepository)
    {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'id of location')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $locationId = $input->getArgument('id');
        $location = $this->locationRepository->findOneBy(['id' => $locationId]);
        $measurements = $this->util->getWeatherForLocation($location);
        $io->writeln(sprintf('Location: %s', $location->getCity()));
        foreach ($measurements as $measurement) {
            $io->writeln(sprintf('date: %s, %s°C', $measurement->getDate()->format('Y-m-d'), $measurement->getCelsius()));
        }
        return Command::SUCCESS;
    }
}
