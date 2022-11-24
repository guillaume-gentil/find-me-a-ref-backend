<?php

namespace App\Command;

use App\Repository\ClubRepository;
use App\Repository\UserRepository;
use App\Repository\ArenaRepository;
use App\Service\GeolocationManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GeocodeCommand extends Command
{
    protected static $defaultName = 'app:geocode:update';
    protected static $defaultDescription = 'Update geocode (lng, lat) in DB according to address';

    private $arenaRepository;
    private $userRepository;
    private $clubRepository;
    private $geolocationManager;
    private $doctrine;

    public function __construct(
        ArenaRepository $arenaRepository, 
        UserRepository $userRepository, 
        ClubRepository $clubRepository,
        GeolocationManager $geolocationManager,
        ManagerRegistry $doctrine
        )
    {
        $this->arenaRepository = $arenaRepository;
        $this->userRepository = $userRepository;
        $this->clubRepository = $clubRepository;
        $this->geolocationManager = $geolocationManager;

        $this->doctrine = $doctrine;

        parent::__construct();
    }

    protected function configure(): void
    {
        // $this
        //     ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
        //     ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        // ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        // get all arrays to fix
        $arenas = $this->arenaRepository->findAll();
        $users = $this->userRepository->findAll();
        $clubs = $this->clubRepository->findAll();

        // update geocodes
        foreach ($arenas as $arena) {
            $arena->setLatitude($this->geolocationManager->useGeocoder($arena->getAddress(), $arena->getZipCode(), 'lat'));
            $arena->setLongitude($this->geolocationManager->useGeocoder($arena->getAddress(), $arena->getZipCode(), 'lng'));
        }

        foreach ($users as $user) {
            $user->setLatitude($this->geolocationManager->useGeocoder($user->getAddress(), $user->getZipCode(), 'lat'));
            $user->setLongitude($this->geolocationManager->useGeocoder($user->getAddress(), $user->getZipCode(), 'lng'));
        }

        foreach ($clubs as $club) {
            $club->setLatitude($this->geolocationManager->useGeocoder($club->getAddress(), $club->getZipCode(), 'lat'));
            $club->setLongitude($this->geolocationManager->useGeocoder($club->getAddress(), $club->getZipCode(), 'lng'));
        }

        // save updates in DB
        $manager = $this->doctrine->getManager();
        $manager->flush();

        // command has success !
        $io->success('Success ! The arenas geocode have been correctly update');

        return Command::SUCCESS;
    }
}
