<?php

namespace App\Tests\Unit\Worker\DTO;

use App\Worker\DTO\TrackDto;
use PHPUnit\Framework\TestCase;

class TrackDtoTest extends TestCase
{
    public function testFromArray(): void
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

        $TrackDto = new TrackDto();
        $TrackDto->fromArray($data);

        $this->assertEquals($data['track']['id'], $TrackDto->getSpotifyId());
        $this->assertEquals(new \DateTime($data['played_at']), $TrackDto->getPlayedAt());
        $this->assertEquals($data['track']['name'], $TrackDto->getName());
        $this->assertEquals($data['track']['artists'][0]['name'], $TrackDto->getArtist());
    }
}
