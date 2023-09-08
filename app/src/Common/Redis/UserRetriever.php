<?php

namespace App\Common\Redis;

use App\Common\Entity\User;
use App\Common\Repository\UserRepository;
use App\Common\Spotify\Client\SpotifyClient;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class UserRetriever
{
    private RedisCache $redis;
    private UserRepository $userRepository;
    private SpotifyClient $client;

    public function __construct(RedisCache $redis, UserRepository $userRepository, SpotifyClient $client)
    {
        $this->redis = $redis;
        $this->userRepository = $userRepository;
        $this->client = $client;
    }

    public function __invoke(): User
    {
        $redisClient = RedisAdapter::createConnection('redis://redis');
        $cacheAdapter = new RedisAdapter($redisClient);

        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $token = $this->client->getTokenFromRefreshToken($user->getRefreshToken());
            $cachePool = $cacheAdapter->getItem('user-'.$user->getSpotifyClientId());
            $cachePool->set($token)
                ->tag(['token']);
            $cacheAdapter->save($cachePool);
        }

        if (!$cachePool) {
            $users = $this->userRepository->findAll();
            $cachePool = $this->redis->saveItem('users', $users);
        }
        \assert($cachePool instanceof User);

        return $cachePool;
    }
}
