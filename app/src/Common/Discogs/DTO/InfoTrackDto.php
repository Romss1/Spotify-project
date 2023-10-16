<?php

namespace App\Common\Discogs\DTO;

class InfoTrackDto
{
    /**
     * @param array<string> $genre
     */
    public function __construct(
        public readonly array $genre,
        public readonly int $releaseYear,
        public readonly string $country,
        public readonly string $cover
    ) {
    }

    /**
     * @param array{
     *     results: array{
     *        0: array{
     *              country: string,
     *              year: string,
     *              genre: array<string>,
     *              cover_image: string
     *          }
     *      }
     * } $data
     */
    public static function fromArray(array $data): InfoTrackDto
    {
        $genre = $data['results'][0]['genre'];
        $releaseYear = (int) $data['results'][0]['year'];
        $country = $data['results'][0]['country'];
        $cover = $data['results'][0]['cover_image'];

        return new self(
            $genre,
            $releaseYear,
            $country,
            $cover
        );
    }
}
