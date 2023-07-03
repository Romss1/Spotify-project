<?php

namespace App\Tests\Unit\Worker\Entity;

use App\Common\Entity\User;
use App\Worker\DTO\TrackDto;
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
        $trackDto = new TrackDto();
        $trackDto->setSpotifyId('example_spotify_id');
        $trackDto->setPlayedAt(new \DateTime('2000-01-01'));
        $trackDto->setName('example_name');
        $trackDto->setArtist('example_artist');

        $track = new Track();
        $track->fromTrackDto($trackDto);

        $this->assertEquals($trackDto->getSpotifyId(), $track->getSpotifyId());
        $this->assertEquals($trackDto->getPlayedAt(), $track->getPlayedAt());
        $this->assertEquals($trackDto->getName(), $track->getName());
        $this->assertEquals($trackDto->getArtist(), $track->getArtist());
    }
}
