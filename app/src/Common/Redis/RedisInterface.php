<?php

namespace App\Common\Redis;

use Predis\ClientInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

interface RedisInterface
{
    public function getRedisClient(): ClientInterface;

    public function getCacheAdapter(): RedisAdapter;

    public function getItem(string $key): mixed;

    public function saveItem(string $key, mixed $value): mixed;

    public function deleteItem(string $key): void;
}
