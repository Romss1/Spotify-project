<?php

namespace App\Tests\Unit\Common\Spotify\DTO;

use App\Common\Spotify\DTO\TrackDto;
use PHPUnit\Framework\TestCase;

final class TrackDtoTest extends TestCase
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

        $TrackDto = TrackDto::fromArray($data);

        $this->assertEquals($data['track']['id'], $TrackDto->spotifyId);
        $this->assertEquals(new \DateTime($data['played_at']), $TrackDto->playedAt);
        $this->assertEquals($data['track']['name'], $TrackDto->name);
        $this->assertEquals($data['track']['artists'][0]['name'], $TrackDto->artist);
    }
}
