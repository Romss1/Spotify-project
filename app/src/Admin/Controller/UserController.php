<?php

namespace App\Admin\Controller;

use App\Common\Entity\User;
use App\Common\Spotify\Client\SpotifyClient;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/callback')]
    public function __invoke(Request $request, SpotifyClient $client, LoggerInterface $logger, EntityManagerInterface $em): Response
    {
        $code = \array_key_exists('code', $request->query->all()) ? $request->query->get('code') : null;
        $state = \array_key_exists('state', $request->query->all()) ? $request->query->get('state') : null;
        $error = \array_key_exists('error', $request->query->all()) ? $request->query->get('error') : null;

//       Raise an exception ?
        if ($error) {
            $logger->error('Unauthorized');

            return $this->render('authorization_failure.html.twig');
        }

        \assert(is_string($code));
        $token = $client->getToken($code);

        $user = new User();
        $user->setToken($token->accessToken);
        $user->setRefreshToken($token->refreshToken);
        $user->setScope($token->scope);
        $user->setLastCallToSpotifyApi(new \DateTime('2000-01-01'));

        $em->persist($user);
        $em->flush();

        return $this->render('authorization_success.html.twig');
    }
}
