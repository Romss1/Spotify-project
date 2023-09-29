<?php

namespace App\Worker\Command;

use App\Common\Entity\User;
use App\Common\Repository\UserRepository;
use App\Common\Spotify\Client\SpotifyClient;
use App\Common\Spotify\Exception\UnauthorizedException;
use App\Worker\Message\Track;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[AsCommand(name: 'get-spotify-plays')]
class GetSpotifyPlays extends Command
{
    //    private const USERS_KEY = 'users';
    private SpotifyClient $spotifyClient;
    private UserRepository $userRepository;
    private SerializerInterface $serializer;
    private MessageBusInterface $bus;

    public function __construct(
        SpotifyClient $spotifyClient,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        MessageBusInterface $bus
    ) {
        parent::__construct();
        $this->spotifyClient = $spotifyClient;
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
        $this->bus = $bus;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Redis
        $redisClient = RedisAdapter::createConnection('redis://redis');
        $cache = new RedisAdapter($redisClient);
        $keys = $redisClient->keys('user-*');
        foreach ($keys as $key) {
            $spotifyClientId = explode('-', $key)[1];
            $user = $this->userRepository->findOneBy(['spotifyClientId' => $spotifyClientId]);
            $token = $cache->getItem($key)->get();
            \assert(is_string($token));
            try {
                $trackDTOs = $this->spotifyClient->getRecentlyPlayedTracks($token);
            } catch (UnauthorizedException) {
                \assert($user instanceof User);
                \assert(is_string($user->getRefreshToken()));
                $token = $this->spotifyClient->getTokenFromRefreshToken($user->getRefreshToken())->accessToken;

                \assert(is_string($token));
                $item = $cache->getItem($key);
                $item->set($token);
                $cache->save($item);

                $trackDTOs = $this->spotifyClient->getRecentlyPlayedTracks($token);
            }

            $i = 0;
            $newLastCallToSpotifyApi = null;
            foreach ($trackDTOs as $trackDTO) {
                \assert($user instanceof User);
                $lastCallToSpotifyApi = $user->getLastCallToSpotifyApi()->getTimestamp();
                if ($lastCallToSpotifyApi >= $trackDTO->playedAt->getTimestamp()) {
                    break;
                }
                $trackDTO->setUserId($user->getId());
                $jsonData = $this->serializer->serialize($trackDTO, 'json');
                $this->bus->dispatch(new Track($jsonData));

                if (0 === $i) {
                    $newLastCallToSpotifyApi = $trackDTO->playedAt;
                    ++$i;
                }
            }
            \assert($user instanceof User);
            if ($newLastCallToSpotifyApi instanceof \DateTime) {
                $user->setLastCallToSpotifyApi($newLastCallToSpotifyApi);
            }
            $this->userRepository->save($user, true);
        }

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you to get Spotify plays for all Users');
    }
}
