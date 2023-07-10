<?php

namespace App\Common\Spotify\DTO;

class TokenDto
{
    public function __construct(
        public readonly string|null $accessToken,
        public readonly string|null $type,
        public readonly int|null $expiredIn,
        public readonly string|null $refreshToken,
        public readonly string|null $scope)
    {
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromArray(array $data): TokenDto
    {
        $accessToken = is_string($data['access_token']) ? $data['access_token'] : null;
        $type = is_string($data['token_type']) ? $data['token_type'] : null;
        $expiredIn = is_int($data['expires_in']) ? $data['expires_in'] : null;
        $refreshToken = is_string($data['refresh_token']) ? $data['refresh_token'] : null;
        $scope = is_string($data['scope']) ? $data['scope'] : null;

        return new self(
            $accessToken,
            $type,
            $expiredIn,
            $refreshToken,
            $scope
        );
    }
}
