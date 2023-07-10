<?php

namespace App\Tests\Functional\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    public function testAuthorizationSuccessPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/callback', ['code' => '1234', 'state' => '1234']);
        $spotifyClient = $client->getContainer()->get('App\Common\Spotify\Client\SpotifyClient');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
//        $this->assertSelectorTextContains('h1', 'Hello World');
    }
}
