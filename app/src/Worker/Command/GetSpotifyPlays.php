<?php

namespace App\Worker\Command;

use App\Admin\Repository\UserRepository;
use App\Common\Client\SpotifyClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'get-spotify-plays')]
class GetSpotifyPlays extends Command
{
    private SpotifyClient $spotifyClient;
    private UserRepository $userRepository;

    public function __construct(SpotifyClient $spotifyClient, UserRepository $userRepository)
    {
        parent::__construct();
        $this->spotifyClient = $spotifyClient;
        $this->userRepository = $userRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            $token = $user->getToken();
            $json = $this->spotifyClient->getRecentlyPlayedTracks($token);
            dd($json);
        }

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you to get Spotify plays for all Users');
    }
}
