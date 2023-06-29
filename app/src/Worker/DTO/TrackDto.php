<?php

namespace App\Worker\DTO;

class TrackDto
{
    private string $spotifyId;
    private \DateTime $playedAt;
    private string $name;
    private string $artist;

    public function __construct()
    {
    }

    public function getSpotifyId(): string
    {
        return $this->spotifyId;
    }

    public function setSpotifyId(string $spotifyId): void
    {
        $this->spotifyId = $spotifyId;
    }

    public function getPlayedAt(): \DateTime
    {
        return $this->playedAt;
    }

    public function setPlayedAt(\DateTime $playedAt): void
    {
        $this->playedAt = $playedAt;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getArtist(): string
    {
        return $this->artist;
    }

    public function setArtist(string $artist): void
    {
        $this->artist = $artist;
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
    public function fromArray(array $data): TrackDto
    {
        $this->setSpotifyId($data['track']['id']);
        $this->setPlayedAt(new \DateTime($data['played_at']));
        $this->setName($data['track']['name']);
        $this->setArtist($data['track']['artists'][0]['name']);

        return $this;
    }
}
