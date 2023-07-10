<?php

namespace App\Common\Spotify\Client;

use App\Common\Spotify\DTO\TokenDto;
use App\Common\Spotify\DTO\TrackDto;
use App\Common\Spotify\Exceptions\InvalidRefreshTokenException;
use App\Common\Spotify\Exceptions\InvalidTokenException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SpotifyClient
{
    private HttpClientInterface $client;

    public function __construct(
        HttpClientInterface $client,
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $redirectUri
    ) {
        $this->client = $client;
    }

    public function getToken(string $code): TokenDto
    {
        $response = $this->client->request('POST', 'https://accounts.spotify.com/api/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic '.\base64_encode($this->clientId.':'.$this->clientSecret),
            ],
            'body' => [
                'grant_type' => 'authorization_code',
                'redirect_uri' => $this->redirectUri,
                'code' => $code,
            ],
        ]);

        if (400 === $response->getStatusCode()) {
            throw new InvalidTokenException();
        }

        return TokenDto::fromArray($response->toArray());
    }

    public function getTokenFromRefreshToken(string $refreshToken): string
    {
        $response = $this->client->request('POST', 'https://accounts.spotify.com/api/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic '.\base64_encode($this->clientId.':'.$this->clientSecret),
            ],
            'body' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ],
        ]);

        if (400 === $response->getStatusCode() || !is_string($response->toArray()['access_token'])) {
            throw new InvalidRefreshTokenException();
        }

        return $response->toArray()['access_token'];
    }

    public function getUserAuthorizationUrl(): ?string
    {
        $params = [
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'scope' => 'user-read-private user-read-email user-read-recently-played',
            'show_dialog' => 'false',
            'state' => uniqid(),
        ];

        $url = 'https://accounts.spotify.com/authorize?'.http_build_query($params);

        $response = $this->client->request('GET', $url);

        if (is_array($response->getInfo()) && array_key_exists('original_url', $response->getInfo())) {
            return $response->getInfo()['original_url'];
        }

        return null;
    }

    public function getRecentlyPlayedTracks(string $token): TrackDto
    {
        $response = $this->client->request('GET', 'https://api.spotify.com/v1/me/player/recently-played', [
            'auth_bearer' => $token,
            'query' => [
                'limit' => 50,
                ],
        ]);

        if (401 === $response->getStatusCode()) {
            throw new InvalidTokenException();
        }

        return TrackDto::fromArray($response->toArray());
    }
}
