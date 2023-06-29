<?php

namespace App\Tests\Unit\Common\Entity;

use App\Common\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $user = new User();

        $token = 'example_token';
        $refreshToken = 'example_refresh_token';
        $scope = 'example_scope';
        $lastCallToSpotifyApi = new \DateTime('2000-01-01');

        $user->setToken($token);
        $user->setRefreshToken($refreshToken);
        $user->setScope($scope);
        $user->setLastCallToSpotifyApi($lastCallToSpotifyApi);

        $this->assertNull($user->getId());
        $this->assertEquals($token, $user->getToken());
        $this->assertEquals($refreshToken, $user->getRefreshToken());
        $this->assertEquals($scope, $user->getScope());
        $this->assertEquals($lastCallToSpotifyApi, $user->getLastCallToSpotifyApi());
    }

    public function testPropertyLengths(): void
    {
        $user = new User();

        $user->setToken(str_repeat('x', 255));
        $user->setRefreshToken(str_repeat('y', 255));
        $user->setScope(str_repeat('z', 255));

        $this->assertEquals(str_repeat('x', 255), $user->getToken());
        $this->assertEquals(str_repeat('y', 255), $user->getRefreshToken());
        $this->assertEquals(str_repeat('z', 255), $user->getScope());
    }
}
