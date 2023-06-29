<?php

namespace App\Worker\Command;

use App\Common\Client\SpotifyClient;
use App\Common\Entity\User;
use App\Common\Repository\UserRepository;
use App\Worker\DTO\TrackDto;
use App\Worker\Entity\Track;
use App\Worker\Repository\TrackRepository;
use Predis\ClientInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'get-spotify-plays')]
class GetSpotifyPlays extends Command
{
    private SpotifyClient $spotifyClient;
    private UserRepository $userRepository;
    private TrackRepository $trackRepository;

    private ClientInterface $redis;

    public function __construct(SpotifyClient $spotifyClient, UserRepository $userRepository, TrackRepository $trackRepository, ClientInterface $redis)
    {
        parent::__construct();
        $this->spotifyClient = $spotifyClient;
        $this->userRepository = $userRepository;
        $this->trackRepository = $trackRepository;
        $this->redis = $redis;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->redis->get('all-users');
        if (!$users) {
            $users = $this->userRepository->findAll();
            $this->redis->set('all-users', serialize($users));
        } else {
            /** @var User[] $users */
            $users = unserialize($users);
        }

        foreach ($users as $user) {
            $token = $user->getToken();
            \assert(is_string($token));
            $response = $this->spotifyClient->getRecentlyPlayedTracks($token);

            if (401 === $response->getStatusCode()) {
                \assert(is_string($user->getRefreshToken()));
                $token = $this->spotifyClient->getTokenFromRefreshToken($user->getRefreshToken())->toArray()['access_token'];
                $user->setToken($token);
                $this->userRepository->save($user, true);
            }
            $response = $this->spotifyClient->getRecentlyPlayedTracks($token);
            $i = 0;
            foreach ($response->toArray()['items'] as $item) {
                $trackDto = new TrackDto();
                $trackDto->fromArray($item);
                if ($user->getLastCallToSpotifyApi()->getTimestamp() >= $trackDto->getPlayedAt()->getTimestamp()) {
                    return Command::SUCCESS;
                }
                $track = new Track();
                $track->fromTrackDto($trackDto);
                $this->trackRepository->save($track, true);
                if (0 === $i) {
                    $user->setLastCallToSpotifyApi($trackDto->getPlayedAt());
                    $this->userRepository->save($user, true);
                    ++$i;
                }
            }
        }

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you to get Spotify plays for all Users');
    }
}
