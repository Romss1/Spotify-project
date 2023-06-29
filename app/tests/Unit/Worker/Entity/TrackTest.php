<?php

namespace App\Tests\Unit\Worker\Entity;

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

        $this->assertNull($track->getId());
        $this->assertEquals($spotifyId, $track->getSpotifyId());
        $this->assertEquals($playedAt, $track->getPlayedAt());
        $this->assertEquals($name, $track->getName());
        $this->assertEquals($artist, $track->getArtist());
    }
}
