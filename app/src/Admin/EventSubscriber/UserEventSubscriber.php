<?php

namespace App\Admin\EventSubscriber;

use App\Admin\Exception\DuplicatedUserException;
use App\Common\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UserEventSubscriber implements EventSubscriber
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array<string>
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof User) {
            return;
        }

        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['spotifyClientId' => $entity->getSpotifyClientId()]);

        if (null !== $existingUser) {
            throw new DuplicatedUserException('A user with the same spotify_client_id already exists in the database.');
        }
    }
}
