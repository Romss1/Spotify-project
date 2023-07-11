<?php

namespace App\Common\Spotify\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SpotifyClient
{
    final public const TOKEN_URI = 'https://accounts.spotify.com/api/token';
    final public const AUTHORIZE_URI = 'https://accounts.spotify.com/authorize?';
    final public const RECENTLY_PLAYED_URI = 'https://api.spotify.com/v1/me/player/recently-played';
    final public const CONTENT_TYPE = 'application/x-www-form-urlencoded';
    final public const GRAND_TYPE_AUTHORIZATION = 'authorization_code';
    final public const GRAND_TYPE_REFRESH_TOKEN = 'refresh_token';
    final public const RESPONSE_TYPE = 'code';
    final public const SCOPE = 'user-read-private user-read-email user-read-recently-played';
    final public const SHOW_DIALOG = 'false';
    final public const QUERY_LIMIT = 50;
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
        return $this->client->request('POST', self::TOKEN_URI, [
            'headers' => [
                'Content-Type' => self::CONTENT_TYPE,
                'Authorization' => 'Basic '.\base64_encode($this->clientId.':'.$this->clientSecret),
            ],
            'body' => [
                'grant_type' => self::GRAND_TYPE_AUTHORIZATION,
                'redirect_uri' => $this->redirectUri,
                'code' => $code,
            ],
        ]);
    }

    public function getTokenFromRefreshToken(string $refreshToken): ResponseInterface
    {
        return $this->client->request('POST', self::TOKEN_URI, [
            'headers' => [
                'Content-Type' => self::CONTENT_TYPE,
                'Authorization' => 'Basic '.\base64_encode($this->clientId.':'.$this->clientSecret),
            ],
            'body' => [
                'grant_type' => self::GRAND_TYPE_REFRESH_TOKEN,
                'refresh_token' => $refreshToken,
            ],
        ]);
    }

    public function getUserAuthorizationUrl(): ?string
    {
        $params = [
            'client_id' => $this->clientId,
            'response_type' => self::RESPONSE_TYPE,
            'redirect_uri' => $this->redirectUri,
            'scope' => self::SCOPE,
            'show_dialog' => self::SHOW_DIALOG,
            'state' => uniqid(),
        ];

        $url = self::AUTHORIZE_URI.http_build_query($params);

        $response = $this->client->request('GET', $url);

        if (is_array($response->getInfo()) && array_key_exists('original_url', $response->getInfo())) {
            return $response->getInfo()['original_url'];
        }

        return null;
    }

    public function getRecentlyPlayedTracks(string $token): ResponseInterface
    {
        $response = $this->client->request('GET', self::RECENTLY_PLAYED_URI, [
            'auth_bearer' => $token,
            'query' => [
                'limit' => self::QUERY_LIMIT,
                ],
        ]);

        return $response;
    }
}
