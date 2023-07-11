<?php

namespace App\Tests\Unit\Common\Spotify\Client;

use PHPUnit\Framework\TestCase;

class SpotifyClientTest extends TestCase
{
//    private SpotifyClient $spotifyClient;
//    private HttpClientInterface $httpClient;
//
//    protected function setUp(): void
//    {
//        $this->httpClient = $this->createMock(HttpClientInterface::class);
//        $this->spotifyClient = new SpotifyClient(
//            $this->httpClient,
//            'client_id',
//            'client_secret',
//            'redirect_uri'
//        );
//    }

//    public function testGetToken(): void
//    {
//        $response = $this->createMock(ResponseInterface::class);
//        $this->httpClient->expects($this->any())
//            ->method('request')
//            ->with('POST', 'https://accounts.spotify.com/api/token', [
//                'headers' => [
//                    'Content-Type' => 'application/x-www-form-urlencoded',
//                    'Authorization' => 'Basic '.base64_encode('client_id:client_secret'),
//                ],
//                'body' => [
//                    'grant_type' => 'authorization_code',
//                    'redirect_uri' => 'redirect_uri',
//                    'code' => 'code',
//                ],
//            ])
//            ->willReturn($response);
//
//        $this->assertSame($response, $this->spotifyClient->getToken('code'));
//    }

//    public function testGetUserAuthorizationUrl(): void
//    {
//        $this->httpClient->expects($this->once())
//            ->method('request')
//            ->with('GET', 'https://accounts.spotify.com/authorize?client_id=client_id&response_type=code&redirect_uri=redirect_uri&scope=user-read-private+user-read-email+user-read-recently-played&show_dialog=false&state=');
//
//        $expectedStart = 'https://accounts.spotify.com/authorize?client_id=client_id&response_type=code&redirect_uri=redirect_uri&scope=user-read-private+user-read-email+user-read-recently-played&show_dialog=false&state=';
//        $this->assertStringStartsWith($expectedStart, $this->spotifyClient->getUserAuthorizationUrl());
//    }

//    public function testGetRecentlyPlayedTracks(): void
//    {
//        $response = $this->createMock(ResponseInterface::class);
//        $this->httpClient->expects($this->once())
//            ->method('request')
//            ->with('GET', 'https://api.spotify.com/v1/me/player/recently-played', [
//                'auth_bearer' => 'token',
//                'query' => [
//                    'limit' => 50,
//                ],
//            ])
//            ->willReturn($response);
//
//        $this->assertSame([], $this->spotifyClient->getRecentlyPlayedTracks('token'));
//    }
}
