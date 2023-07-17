<?php

namespace App\Tests\Unit\Common\Spotify\DTO;

use App\Common\Spotify\DTO\TokenDto;
use PHPUnit\Framework\TestCase;

final class TokenTest extends TestCase
{
    public function testFromArrayWithoutRefreshToken(): void
    {
        // Given
        $data = [
            'access_token' => 'tokenTest',
            'token_type' => 'typeTest',
            'expires_in' => 999999999,
            'scope' => 'scopeTest',
            'refresh_token' => 'refreshToken',
        ];

        // When
        $tokenDTO = TokenDto::fromArray($data);

        // Then
        $this->assertEquals($data['access_token'], $tokenDTO->accessToken);
        $this->assertEquals($data['token_type'], $tokenDTO->type);
        $this->assertEquals($data['expires_in'], $tokenDTO->expiredIn);
        $this->assertEquals($data['scope'], $tokenDTO->scope);
        $this->assertEquals($data['refresh_token'], $tokenDTO->refreshToken);
    }

    public function testFromArrayWithRefreshToken(): void
    {
        // Given
        $data = [
            'access_token' => 'tokenTest',
            'token_type' => 'typeTest',
            'expires_in' => 999999999,
            'scope' => 'scopeTest',
        ];

        $refreshToken = 'refreshTokenTest';

        // When
        $tokenDTO = TokenDto::fromArray($data, $refreshToken);

        // Then
        $this->assertEquals($data['access_token'], $tokenDTO->accessToken);
        $this->assertEquals($data['token_type'], $tokenDTO->type);
        $this->assertEquals($data['expires_in'], $tokenDTO->expiredIn);
        $this->assertEquals($data['scope'], $tokenDTO->scope);
        $this->assertEquals($refreshToken, $tokenDTO->refreshToken);
    }
}
