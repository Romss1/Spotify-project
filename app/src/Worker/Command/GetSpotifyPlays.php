<?php

namespace App\Worker\Command;

use App\Common\Entity\User;
use App\Common\Repository\UserRepository;
use App\Common\Spotify\Client\SpotifyClient;
use App\Common\Spotify\Exception\UnauthorizedException;
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
            try {
                $trackDTOs = $this->spotifyClient->getRecentlyPlayedTracks($token);
            } catch (UnauthorizedException) {
                \assert(is_string($user->getRefreshToken()));
                $token = $this->spotifyClient->getTokenFromRefreshToken($user->getRefreshToken())->accessToken;

                \assert(is_string($token));
                $user->setToken($token);
                $cachePool->set($user);
                $cacheAdapter->save($cachePool);
                $this->userRepository->save($user, true);
                $trackDTOs = $this->spotifyClient->getRecentlyPlayedTracks($token);
            }

            $newLastCallToSpotifyApi = null;
            $i = 0;
            foreach ($trackDTOs as $trackDTO) {
                if ($user->getLastCallToSpotifyApi()->getTimestamp() >= $trackDTO->playedAt->getTimestamp()) {
                    break;
                }
                $track = new Track();
                $track->fromTrackDto($trackDTO);
                $track->setUser($user);

                $this->trackRepository->save($track, true);
                if (0 === $i) {
                    $newLastCallToSpotifyApi = $trackDTO->playedAt;
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
