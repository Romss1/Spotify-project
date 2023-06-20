<?php

namespace App\Tests\Admin\Entity\DTO\Spotify;

use App\Admin\Entity\DTO\Spotify\Token;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    public function testGetAccessToken()
    {
        $token = new Token('access_token', 'type', 3600, 'refresh_token', 'scope');
        $this->assertEquals('access_token', $token->getAccessToken());
    }

    public function testGetType()
    {
        $token = new Token('access_token', 'type', 3600, 'refresh_token', 'scope');
        $this->assertEquals('type', $token->getType());
    }

    public function testGetExpiredIn()
    {
        $token = new Token('access_token', 'type', 3600, 'refresh_token', 'scope');
        $this->assertEquals(3600, $token->getExpiredIn());
    }

    public function testGetRefreshToken()
    {
        $token = new Token('access_token', 'type', 3600, 'refresh_token', 'scope');
        $this->assertEquals('refresh_token', $token->getRefreshToken());
    }

    public function testGetScope()
    {
        $token = new Token('access_token', 'type', 3600, 'refresh_token', 'scope');
        $this->assertEquals('scope', $token->getScope());
    }
}
