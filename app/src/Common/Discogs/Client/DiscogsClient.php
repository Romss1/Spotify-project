<?php

namespace App\Common\Discogs\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class DiscogsClient
{
    public const TOKEN_URI = 'https://api.discogs.com/oauth/request_token';
    private HttpClientInterface $client;

    public function __construct(
        HttpClientInterface $client,
        private readonly string $consumerKey,
        private readonly string $consumerSecret
    ) {
        $this->client = $client;
    }

    public function getFirstResultWithParameters(string ...$parameters): string
    {
        $query = implode('+', $parameters);

        $options = [
            'headers' => [
                'Authorization' => 'Discogs key='.$this->consumerKey.', secret='.$this->consumerSecret,
            ],
            'query' => [
                'q' => $query,
                'per_page' => 1,
                'page' => 1,
            ],
        ];

        $response = $this->client->request('GET', 'https://api.discogs.com/database/search', $options);

        return $response->getContent();
    }
}
