<?php

namespace App\Admin\Controller;

use App\Admin\Exception\DuplicatedUserException;
use App\Common\Entity\User;
use App\Common\Repository\UserRepository;
use App\Common\Spotify\Client\SpotifyClient;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/callback')]
    public function __invoke(Request $request, SpotifyClient $client, LoggerInterface $logger, EntityManagerInterface $em, UserRepository $userRepository): Response
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

        // Redis
        $redisClient = RedisAdapter::createConnection('redis://redis');
        $cacheAdapter = new RedisAdapter($redisClient);
        $cachePool = $cacheAdapter->getItem('user-'.$client->getClientId());
        $cachePool->set($token->accessToken);
        $cacheAdapter->save($cachePool);

        $user = new User();
        \assert(is_string($token->refreshToken));
        $user->setRefreshToken($token->refreshToken);
        \assert(is_string($token->scope));
        $user->setScope($token->scope);
        $user->setLastCallToSpotifyApi(new \DateTime('2000-01-01'));
        $user->setSpotifyClientId($client->getClientId());

        $em->persist($user);

        try {
            $em->flush();
        } catch (DuplicatedUserException $e) {
            return $this->render('authorization_requested_again.html.twig');
        }

        return $this->render('authorization_success.html.twig');
    }
}
