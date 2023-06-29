<?php

namespace App\Admin\DTO\Spotify;

class TokenDto
{
    private string $accessToken;
    private string $type;
    private int $expiredIn;
    private string $refreshToken;
    private string $scope;

    public function __construct(string $accessToken, string $type, int $expiredIn, string $refreshToken, string $scope)
    {
        $this->accessToken = $accessToken;
        $this->type = $type;
        $this->expiredIn = $expiredIn;
        $this->refreshToken = $refreshToken;
        $this->scope = $scope;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getExpiredIn(): int
    {
        return $this->expiredIn;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getScope(): string
    {
        return $this->scope;
    }
}
