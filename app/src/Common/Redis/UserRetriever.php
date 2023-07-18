<?php

namespace App\Common\Redis;

use App\Common\Entity\User;
use App\Common\Repository\UserRepository;

class UserRetriever
{
    private RedisCache $redis;
    private UserRepository $userRepository;

    public function __construct(RedisCache $redis, UserRepository $userRepository)
    {
        $this->redis = $redis;
        $this->userRepository = $userRepository;
    }

    public function __invoke(): User
    {
        $cachePool = $this->redis->getItem('users');

        if (!$cachePool) {
            $users = $this->userRepository->findAll();
            $cachePool = $this->redis->saveItem('users', $users);
        }
        \assert($cachePool instanceof User);

        return $cachePool;
    }
}
