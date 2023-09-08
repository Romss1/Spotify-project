<?php

namespace App\Tests\Unit\Common\Entity;

use App\Common\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $user = new User();

        $token = 'example_token';
        $refreshToken = 'example_refresh_token';
        $scope = 'example_scope';
        $lastCallToSpotifyApi = new \DateTime('2000-01-01');

        $user->setRefreshToken($refreshToken);
        $user->setScope($scope);
        $user->setLastCallToSpotifyApi($lastCallToSpotifyApi);
        $user->setTracks(new ArrayCollection());

        $this->assertNull($user->getId());
        $this->assertEquals($refreshToken, $user->getRefreshToken());
        $this->assertEquals($scope, $user->getScope());
        $this->assertEquals($lastCallToSpotifyApi, $user->getLastCallToSpotifyApi());
        $this->assertInstanceOf(ArrayCollection::class, $user->getTracks());
    }

    public function testPropertyLengths(): void
    {
        $user = new User();

        $user->setRefreshToken(str_repeat('y', 255));
        $user->setScope(str_repeat('z', 255));
        $this->assertEquals(str_repeat('y', 255), $user->getRefreshToken());
        $this->assertEquals(str_repeat('z', 255), $user->getScope());
    }
}
