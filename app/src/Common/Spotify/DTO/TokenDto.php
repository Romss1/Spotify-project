<?php

namespace App\Common\Spotify\DTO;

use App\Common\Spotify\Exceptions\InvalidArgumentException;

class TokenDto
{
    public function __construct(
        public readonly string $accessToken,
        public readonly string $type,
        public readonly int $expiredIn,
        public readonly string $refreshToken,
        public readonly string $scope)
    {
    }

    /**
     * @param array<mixed> $data
     *
     * @throws InvalidArgumentException
     */
    public static function fromArray(array $data): TokenDto
    {
        $accessToken = is_string($data['access_token']) ? $data['access_token'] : throw new InvalidArgumentException('Invalid access token');
        $type = is_string($data['token_type']) ? $data['token_type'] : throw new InvalidArgumentException('Invalid token type');
        $expiredIn = is_int($data['expires_in']) ? $data['expires_in'] : throw new InvalidArgumentException('Invalid expired in');
        $refreshToken = is_string($data['refresh_token']) ? $data['refresh_token'] : throw new InvalidArgumentException('Invalid refresh token');
        $scope = is_string($data['scope']) ? $data['scope'] : throw new InvalidArgumentException('Invalid scope');

        return new self(
            $accessToken,
            $type,
            $expiredIn,
            $refreshToken,
            $scope
        );
    }
}
