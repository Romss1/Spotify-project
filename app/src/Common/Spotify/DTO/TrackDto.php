<?php

namespace App\Common\Spotify\DTO;

use App\Common\Spotify\Exceptions\InvalidArgumentException;

class TrackDto
{
    public function __construct(
        public readonly string $spotifyId,
        public readonly \DateTime $playedAt,
        public readonly string $name,
        public readonly string $artist
    ) {
    }

    /**
     * @param array<mixed> $data
     *
     * @throws InvalidArgumentException
     */
    public static function fromArray(array $data): TrackDto
    {
        $spotifyId = is_string($data['track']['id']) ? $data['track']['id'] : throw new InvalidArgumentException('Invalid track id');
        $playedAt = (new \DateTime($data['played_at']) instanceof \DateTime) ? new \DateTime($data['played_at']) : throw new InvalidArgumentException('Invalid date');
        $name = is_string($data['track']['name']) ? $data['track']['name'] : throw new InvalidArgumentException('Invalid track name');
        $artist = is_string($data['track']['artists'][0]['name']) ? $data['track']['artists'][0]['name'] : throw new InvalidArgumentException('Invalid artist name');

        return new self(
            $spotifyId,
            $playedAt,
            $name,
            $artist
        );
    }
}
