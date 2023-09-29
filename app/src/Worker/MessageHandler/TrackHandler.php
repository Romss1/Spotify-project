<?php

namespace App\Worker\MessageHandler;

use App\Common\Entity\User;
use App\Common\Repository\UserRepository;
use App\Common\Spotify\DTO\TrackDto;
use App\Worker\Entity\Track as TrackEntity;
use App\Worker\Message\Track;
use App\Worker\Repository\TrackRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;

#[AsMessageHandler]
class TrackHandler
{
    private SerializerInterface $serializer;
    private TrackRepository $trackRepository;
    private UserRepository $userRepository;

    public function __construct(SerializerInterface $serializer, TrackRepository $trackRepository, UserRepository $userRepository)
    {
        $this->trackRepository = $trackRepository;
        $this->serializer = $serializer;
        $this->userRepository = $userRepository;
    }

    public function __invoke(Track $message): void
    {
        $trackDto = $this->serializer->deserialize($message->getContent(), TrackDto::class, 'json');
        $track = new TrackEntity();
        $track->fromTrackDto($trackDto);

        $user = $this->userRepository->find($trackDto->getUserId());

        if ($user instanceof User) {
            $track->setUser($user);
        }

        $this->trackRepository->save($track, true);
    }
}
