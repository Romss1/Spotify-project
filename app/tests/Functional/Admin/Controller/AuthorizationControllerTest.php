<?php

namespace App\Tests\Functional\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationControllerTest extends WebTestCase
{
    public function testAuthorizationPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/authorize');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
