<?php

namespace App\Common\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

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

    public function getToken(string $code): ResponseInterface
    {
        return $this->client->request('POST', 'https://accounts.spotify.com/api/token', [
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
    }

    public function getTokenFromRefreshToken(string $refreshToken): ResponseInterface
    {
        return $this->client->request('POST', 'https://accounts.spotify.com/api/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic '.\base64_encode($this->clientId.':'.$this->clientSecret),
            ],
            'body' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ],
        ]);
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

    public function getRecentlyPlayedTracks(string $token): ResponseInterface
    {
        $response = $this->client->request('GET', 'https://api.spotify.com/v1/me/player/recently-played', [
            'auth_bearer' => $token,
            'query' => [
                'limit' => 50,
                ],
        ]);

        return $response;
    }
}
