<?php

namespace App\Worker\Command;

use App\Common\Redis\RedisCache;
use App\Common\Redis\UserRetriever;
use App\Common\Repository\UserRepository;
use App\Common\Spotify\Client\SpotifyClient;
use App\Common\Spotify\Exception\UnauthorizedException;
use App\Worker\Entity\Track;
use App\Worker\Repository\TrackRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'get-spotify-plays')]
class GetSpotifyPlays extends Command
{
    private const USERS_KEY = 'users';
    private SpotifyClient $spotifyClient;
    private UserRepository $userRepository;
    private TrackRepository $trackRepository;
    private UserRetriever $userRetriever;
    private RedisCache $redisCache;

    public function __construct(SpotifyClient $spotifyClient, UserRepository $userRepository, TrackRepository $trackRepository, UserRetriever $userRetriever, RedisCache $redisCache)
    {
        parent::__construct();
        $this->spotifyClient = $spotifyClient;
        $this->userRepository = $userRepository;
        $this->trackRepository = $trackRepository;
        $this->userRetriever = $userRetriever;
        $this->redisCache = $redisCache;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // TODO userRetrieve doit return une collection de User
        $users = ($this->userRetriever)();

        // TODO update pas le user mais en créé un autre
        foreach ([$users] as $user) {
            $token = $user->getToken();
            \assert(is_string($token));
            try {
                $trackDTOs = $this->spotifyClient->getRecentlyPlayedTracks($token);
            } catch (UnauthorizedException) {
                \assert(is_string($user->getRefreshToken()));
                $token = $this->spotifyClient->getTokenFromRefreshToken($user->getRefreshToken())->accessToken;

                \assert(is_string($token));
                $user->setToken($token);
                $this->redisCache->saveItem(self::USERS_KEY, $user);
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
                $this->redisCache->saveItem(self::USERS_KEY, $user);
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
