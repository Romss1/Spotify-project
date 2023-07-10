<?php

namespace App\Tests\Unit\Worker\Entity;

use App\Common\Entity\User;
use App\Common\Spotify\DTO\TrackDto;
use App\Worker\Entity\Track;
use PHPUnit\Framework\TestCase;

class TrackTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $track = new Track();

        $spotifyId = 'example_spotify_id';
        $playedAt = new \DateTime('2000-01-01');
        $name = 'example_name';
        $artist = 'example_artist';

        $track->setSpotifyId($spotifyId);
        $track->setPlayedAt($playedAt);
        $track->setName($name);
        $track->setArtist($artist);
        $track->setUser(new User());

        $this->assertNull($track->getId());
        $this->assertEquals($spotifyId, $track->getSpotifyId());
        $this->assertEquals($playedAt, $track->getPlayedAt());
        $this->assertEquals($name, $track->getName());
        $this->assertEquals($artist, $track->getArtist());
        $this->assertInstanceOf(User::class, $track->getUser());
    }

    public function testFromTrackDto(): void
    {
        $data = [
            'track' => [
                'id' => 'example_spotify_id',
                'name' => 'example_name',
                'artists' => [
                    [
                        'name' => 'example_artist',
                    ],
                ],
            ],
            'played_at' => '2000-01-01',
        ];
        $trackDto = TrackDto::fromArray($data);

        $track = new Track();
        $track->fromTrackDto($trackDto);

        $this->assertEquals($trackDto->spotifyId, $track->getSpotifyId());
        $this->assertEquals($trackDto->playedAt, $track->getPlayedAt());
        $this->assertEquals($trackDto->name, $track->getName());
        $this->assertEquals($trackDto->artist, $track->getArtist());
    }
}
