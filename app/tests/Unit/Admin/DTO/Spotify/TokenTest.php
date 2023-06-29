<?php

namespace App\Tests\Unit\Admin\DTO\Spotify;

use App\Admin\DTO\Spotify\TokenDto;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    public function testGetAccessToken(): void
    {
        $token = new TokenDto('access_token', 'type', 3600, 'refresh_token', 'scope');
        $this->assertEquals('access_token', $token->getAccessToken());
    }

    public function testGetType(): void
    {
        $token = new TokenDto('access_token', 'type', 3600, 'refresh_token', 'scope');
        $this->assertEquals('type', $token->getType());
    }

    public function testGetExpiredIn(): void
    {
        $token = new TokenDto('access_token', 'type', 3600, 'refresh_token', 'scope');
        $this->assertEquals(3600, $token->getExpiredIn());
    }

    public function testGetRefreshToken(): void
    {
        $token = new TokenDto('access_token', 'type', 3600, 'refresh_token', 'scope');
        $this->assertEquals('refresh_token', $token->getRefreshToken());
    }

    public function testGetScope(): void
    {
        $token = new TokenDto('access_token', 'type', 3600, 'refresh_token', 'scope');
        $this->assertEquals('scope', $token->getScope());
    }
}
