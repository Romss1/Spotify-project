<?php

namespace App\Worker\Entity\DTO\Spotify;

class Token
{
    private string $accessToken;
    private string $type;
    private int $expiredIn;
    private string $refreshToken;
    private string $scope;

    /**
     * @param string $accessToken
     * @param string $type
     * @param int $expiredIn
     * @param string $refreshToken
     * @param string $scope
     */
    public function __construct(string $accessToken, string $type, int $expiredIn, string $refreshToken, string $scope)
    {
        $this->accessToken = $accessToken;
        $this->type = $type;
        $this->expiredIn = $expiredIn;
        $this->refreshToken = $refreshToken;
        $this->scope = $scope;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getExpiredIn(): int
    {
        return $this->expiredIn;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }
}
