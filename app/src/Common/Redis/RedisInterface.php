<?php

namespace App\Common\Redis;

interface RedisInterface
{
    public function getItem(string $key): mixed;

    public function saveItem(string $key, mixed $value): mixed;

    public function deleteItem(string $key): void;
}
