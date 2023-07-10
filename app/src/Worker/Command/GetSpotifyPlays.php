<?php

namespace App\Worker\Command;

use App\Common\Entity\User;
use App\Common\Repository\UserRepository;
use App\Common\Spotify\Client\SpotifyClient;
use App\Common\Spotify\DTO\TrackDto;
use App\Common\Spotify\Exceptions\InvalidRefreshTokenException;
use App\Common\Spotify\Exceptions\InvalidTokenException;
use App\Worker\Entity\Track;
use App\Worker\Repository\TrackRepository;
use Psr\Log\LoggerInterface;
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
    private LoggerInterface $logger;

    public function __construct(SpotifyClient $spotifyClient, UserRepository $userRepository, TrackRepository $trackRepository, LoggerInterface $logger)
    {
        parent::__construct();
        $this->spotifyClient = $spotifyClient;
        $this->userRepository = $userRepository;
        $this->trackRepository = $trackRepository;
        $this->logger = $logger;
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
            $tracks = null;
            try {
                $tracks = $this->spotifyClient->getRecentlyPlayedTracks($token);
            } catch (InvalidTokenException $e) {
                \assert(is_string($user->getRefreshToken()));
                try {
                    $token = $this->spotifyClient->getTokenFromRefreshToken($user->getRefreshToken());
                    $user->setToken($token);
                    $cachePool->set($user);
                    $cacheAdapter->save($cachePool);
                    $this->userRepository->save($user, true);
                    $tracks = $this->spotifyClient->getRecentlyPlayedTracks($token);
                } catch (InvalidRefreshTokenException $e) {
                    $this->logger->error('Invalid refresh token for user id '.$user->getId());
                }
            }

            if (null === $tracks) {
                continue;
            }

            $newLastCallToSpotifyApi = null;
            $i = 0;
            foreach ($tracks as $item) {
                $trackDto = TrackDto::fromArray($item);
                if ($user->getLastCallToSpotifyApi()->getTimestamp() >= $trackDto->playedAt->getTimestamp()) {
                    break;
                }
                $track = new Track();
                $track->fromTrackDto($trackDto);
                $track->setUser($user);

                $this->trackRepository->save($track, true);
                if (0 === $i) {
                    $newLastCallToSpotifyApi = $trackDto->playedAt;
                    ++$i;
                }
            }

            if (!is_null($newLastCallToSpotifyApi)) {
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
