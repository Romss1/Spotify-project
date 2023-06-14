<?php

namespace App\Tests\Admin\Entity;

use App\Admin\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $user = new User();

        $token = 'example_token';
        $refreshToken = 'example_refresh_token';
        $scope = 'example_scope';

        $user->setToken($token);
        $user->setRefreshToken($refreshToken);
        $user->setScope($scope);

        $this->assertNull($user->getId());
        $this->assertEquals($token, $user->getToken());
        $this->assertEquals($refreshToken, $user->getRefreshToken());
        $this->assertEquals($scope, $user->getScope());
    }

    public function testPropertyLengths()
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
