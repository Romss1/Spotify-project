<?php

namespace App\Tests\Unit\Common\Spotify\DTO;

use App\Common\Spotify\DTO\TokenDto;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    public function testGetAccessToken(): void
    {
        $token = new TokenDto('access_token', 'type', 3600, 'refresh_token', 'scope');
        $this->assertEquals('access_token', $token->accessToken);
    }

    public function testGetType(): void
    {
        $token = new TokenDto('access_token', 'type', 3600, 'refresh_token', 'scope');
        $this->assertEquals('type', $token->type);
    }

    public function testGetExpiredIn(): void
    {
        $token = new TokenDto('access_token', 'type', 3600, 'refresh_token', 'scope');
        $this->assertEquals(3600, $token->expiredIn);
    }

    public function testGetRefreshToken(): void
    {
        $token = new TokenDto('access_token', 'type', 3600, 'refresh_token', 'scope');
        $this->assertEquals('refresh_token', $token->refreshToken);
    }

    public function testGetScope(): void
    {
        $token = new TokenDto('access_token', 'type', 3600, 'refresh_token', 'scope');
        $this->assertEquals('scope', $token->scope);
    }
}
