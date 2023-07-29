<?php

namespace App\Common\Redis;

use Predis\Client;
use Predis\ClientInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisCache implements RedisInterface
{
    private RedisAdapter $cacheAdapter;

    public function __construct(private readonly string $redisUrl)
    {
        $redisClient = RedisAdapter::createConnection($this->redisUrl);
        $this->cacheAdapter = new RedisAdapter($redisClient);
    }

    public function getRedisClient(): ClientInterface
    {
        $redis = RedisAdapter::createConnection($this->redisUrl);
        if (!$redis instanceof ClientInterface) {
            $redis = new Client();
        }

        return $redis;
    }

    public function getCacheAdapter(): RedisAdapter
    {
        return $this->cacheAdapter;
    }

    public function getItem(string $key): mixed
    {
        $cacheItem = $this->cacheAdapter->getItem($key);
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        return null;
    }

    public function saveItem(string $key, mixed $value): mixed
    {
        $cacheItem = $this->cacheAdapter->getItem($key);
        $cacheItem->set($value);
        $this->cacheAdapter->save($cacheItem);

        return $cacheItem->get();
    }

    public function deleteItem(string $key): void
    {
        $this->cacheAdapter->deleteItem($key);
    }
}
