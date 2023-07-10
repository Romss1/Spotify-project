<?php

namespace App\Common\Spotify\DTO;

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
     * @param array{
     *     track: array{
     *         id: string,
     *         name: string,
     *         artists: array{
     *             0: array{
     *                 name: string
     *             }
     *         }
     *     },
     *     played_at: string
     * } $data
     */
    public static function fromArray(array $data): TrackDto
    {
        $spotifyId = $data['track']['id'];
        $playedAt = new \DateTime($data['played_at']);
        $name = $data['track']['name'];
        $artist = $data['track']['artists'][0]['name'];

        return new self(
            $spotifyId,
            $playedAt,
            $name,
            $artist
        );
    }
}
