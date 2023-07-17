<?php

namespace App\Tests\Unit\Common\Spotify\Client;

use App\Common\Spotify\Client\SpotifyClient;
use App\Common\Spotify\DTO\TokenDto;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SpotifyClientTest extends TestCase
{
    private SpotifyClient $spotifyClient;
    private HttpClientInterface|MockObject $httpClientMock;

    private ResponseInterface|MockObject $responseMock;

    protected function setUp(): void
    {
        $this->httpClientMock = $this->createMock(HttpClientInterface::class);
        $this->spotifyClient = new SpotifyClient(
            $this->httpClientMock,
            'client_id',
            'client_secret',
            'redirect_uri'
        );
        $this->responseMock = $this->createMock(ResponseInterface::class);
    }

    public function testGetToken(): void
    {
        $code = 'dummy-code';
        $responseBody = [
            'access_token' => 'tokenTest',
             'token_type' => 'typeTest',
             'expires_in' => 999999999,
             'scope' => 'scopeTest',
             'refresh_token' => 'refreshToken',
        ];

        \assert($this->responseMock instanceof MockObject);
        $this->responseMock->method('toArray')->willReturn($responseBody);

        \assert($this->httpClientMock instanceof MockObject);
        $this->httpClientMock->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('POST'),
                $this->equalTo(SpotifyClient::TOKEN_URI),
                $this->callback(function ($options) use ($code) {
                    $this->assertEquals('Basic '.base64_encode('client_id:client_secret'), $options['headers']['Authorization']);
                    $this->assertEquals('redirect_uri', $options['body']['redirect_uri']);
                    $this->assertEquals($code, $options['body']['code']);

                    return true;
                })
            )
            ->willReturn($this->responseMock);

        $tokenDto = $this->spotifyClient->getToken($code);

        $this->assertInstanceOf(TokenDto::class, $tokenDto);
        $this->assertEquals('tokenTest', $tokenDto->accessToken);
        $this->assertEquals('typeTest', $tokenDto->type);
        $this->assertEquals('refreshToken', $tokenDto->refreshToken);
        $this->assertEquals('scopeTest', $tokenDto->scope);
        $this->assertEquals(999999999, $tokenDto->expiredIn);
    }

    public function testGetTokenFromRefreshToken(): void
    {
        $refreshToken = 'refreshToken';
        $responseBody = [
            'access_token' => 'tokenTest',
            'token_type' => 'typeTest',
            'expires_in' => 999999999,
            'scope' => 'scopeTest',
        ];

        \assert($this->responseMock instanceof MockObject);
        $this->responseMock->method('toArray')->willReturn($responseBody);

        \assert($this->httpClientMock instanceof MockObject);
        $this->httpClientMock->expects($this->once())
           ->method('request')
           ->with(
               $this->equalTo('POST'),
               $this->equalTo(SpotifyClient::TOKEN_URI),
               $this->callback(function ($options) use ($refreshToken) {
                   $this->assertEquals('Basic '.base64_encode('client_id:client_secret'), $options['headers']['Authorization']);
                   $this->assertEquals($refreshToken, $options['body']['refresh_token']);

                   return true;
               })
           )
            ->willReturn($this->responseMock);

        $tokenDto = $this->spotifyClient->getTokenFromRefreshToken($refreshToken);

        $this->assertInstanceOf(TokenDto::class, $tokenDto);
        $this->assertEquals('tokenTest', $tokenDto->accessToken);
        $this->assertEquals('typeTest', $tokenDto->type);
        $this->assertEquals('refreshToken', $tokenDto->refreshToken);
        $this->assertEquals('scopeTest', $tokenDto->scope);
        $this->assertEquals(999999999, $tokenDto->expiredIn);
    }

    #[TestWith(['original_url', 'https://dummy-url.com'])]
    #[TestWith(['false_key', null])]
    public function testGetUserAuthorizationUrl(string $key, ?string $url): void
    {
        $infos = [$key => 'https://dummy-url.com'];

        \assert($this->responseMock instanceof MockObject);
        $this->responseMock->method('getInfo')->willReturn($infos);

        \assert($this->httpClientMock instanceof MockObject);
        $this->httpClientMock->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('GET'),
            )
            ->willReturn($this->responseMock);

        $response = $this->spotifyClient->getUserAuthorizationUrl();

        $this->assertEquals($url, $response);
    }
}
