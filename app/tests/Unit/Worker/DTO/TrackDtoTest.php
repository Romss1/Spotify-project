<?php

namespace App\Tests\Unit\Worker\DTO;

use App\Common\Spotify\DTO\TrackDto;
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

        $trackDto = TrackDto::fromArray($data);

        $this->assertEquals($data['track']['id'], $trackDto->spotifyId);
        $this->assertEquals(new \DateTime($data['played_at']), $trackDto->playedAt);
        $this->assertEquals($data['track']['name'], $trackDto->name);
        $this->assertEquals($data['track']['artists'][0]['name'], $trackDto->artist);
    }
}
