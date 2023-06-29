<?php

namespace App\Admin\Controller;

use App\Admin\Form\UserType;
use App\Common\Client\SpotifyClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorizationController extends AbstractController
{
    #[Route('/authorize')]
    public function __invoke(Request $request, SpotifyClient $client): Response
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && is_string($client->getUserAuthorizationUrl())) {
            return $this->redirect($client->getUserAuthorizationUrl());
        }

        return $this->render('authorization.html.twig', [
            'form' => $form,
        ]);
    }
}
