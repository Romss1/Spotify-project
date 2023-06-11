<?php

namespace App\Admin\Controller;

use App\Admin\Entity\DTO\Spotify\Token;
use App\Common\Client\SpotifyClient;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/callback')]
    public function __invoke(Request $request, SpotifyClient $client, LoggerInterface $logger): Response
    {
        $code = \array_key_exists('code', $request->query->all()) ? $request->query->get('code') : null;
        $state = \array_key_exists('state', $request->query->all()) ? $request->query->get('state') : null;
        $error = \array_key_exists('error', $request->query->all()) ? $request->query->get('error') : null;

//       Raise an exception ?
        if (!$code && !$state && $error) {
            $logger->error('Unauthorized');
        }

        \assert(is_string($code));
        $response = $client->getToken($code)->toArray();

        $token = new Token(
            $response['access_token'],
            $response['token_type'],
            $response['expires_in'],
            $response['refresh_token'],
            $response['scope']
        );

        $client->getRecentlyPlayedTracks($token->getAccessToken());
    }
}
