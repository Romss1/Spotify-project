<?php

namespace App\Worker\Command;

use App\Common\Client\SpotifyClient;
use App\Common\Entity\User;
use App\Common\Repository\UserRepository;
use App\Worker\DTO\TrackDto;
use App\Worker\Entity\Track;
use App\Worker\Repository\TrackRepository;
use Symfony\Component\Cache\Adapter\RedisAdapter;
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

    public function __construct(SpotifyClient $spotifyClient, UserRepository $userRepository, TrackRepository $trackRepository)
    {
        parent::__construct();
        $this->spotifyClient = $spotifyClient;
        $this->userRepository = $userRepository;
        $this->trackRepository = $trackRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $redisClient = RedisAdapter::createConnection('redis://redis');
        $cacheAdapter = new RedisAdapter($redisClient);
        $cachePool = $cacheAdapter->getItem('users');
        if (!$cachePool->isHit()) {
            $users = $this->userRepository->findAll();
            $cachePool->set($users);
            $cacheAdapter->save($cachePool);
        } else {
            /** @var User[] $users */
            $users = $cachePool->get();
        }

        foreach ($users as $user) {
            $token = $user->getToken();
            \assert(is_string($token));
            $response = $this->spotifyClient->getRecentlyPlayedTracks($token);

            if (401 === $response->getStatusCode()) {
                \assert(is_string($user->getRefreshToken()));
                $token = $this->spotifyClient->getTokenFromRefreshToken($user->getRefreshToken())->toArray()['access_token'];

                $user->setToken($token);
                $cachePool->set($user);
                $cacheAdapter->save($cachePool);
                $this->userRepository->save($user, true);
                $response = $this->spotifyClient->getRecentlyPlayedTracks($token);
            }

            $newLastCallToSpotifyApi = null;
            $i = 0;
            foreach ($response->toArray()['items'] as $item) {
                $trackDto = new TrackDto();
                $trackDto->fromArray($item);
                if ($user->getLastCallToSpotifyApi()->getTimestamp() >= $trackDto->getPlayedAt()->getTimestamp()) {
                    break;
                }
                $track = new Track();
                $track->fromTrackDto($trackDto);
                $track->setUser($user);

                $this->trackRepository->save($track, true);
                if (0 === $i) {
                    $newLastCallToSpotifyApi = $trackDto->getPlayedAt();
                    ++$i;
                }
            }

            if ($newLastCallToSpotifyApi) {
                $user->setLastCallToSpotifyApi($newLastCallToSpotifyApi);
                $cachePool->set($user);
                $cacheAdapter->save($cachePool);
                $this->userRepository->save($user, true);
            }
        }

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you to get Spotify plays for all Users');
    }
}
