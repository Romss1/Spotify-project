<?php

namespace App\Factory;

use App\Worker\Entity\Track;
use App\Worker\Repository\TrackRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Track>
 *
 * @method        Track|Proxy                     create(array|callable $attributes = [])
 * @method static Track|Proxy                     createOne(array $attributes = [])
 * @method static Track|Proxy                     find(object|array|mixed $criteria)
 * @method static Track|Proxy                     findOrCreate(array $attributes)
 * @method static Track|Proxy                     first(string $sortedField = 'id')
 * @method static Track|Proxy                     last(string $sortedField = 'id')
 * @method static Track|Proxy                     random(array $attributes = [])
 * @method static Track|Proxy                     randomOrCreate(array $attributes = [])
 * @method static TrackRepository|RepositoryProxy repository()
 * @method static Track[]|Proxy[]                 all()
 * @method static Track[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Track[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Track[]|Proxy[]                 findBy(array $attributes)
 * @method static Track[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Track[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Proxy<Track> create(array|callable $attributes = [])
 * @phpstan-method static Proxy<Track> createOne(array $attributes = [])
 * @phpstan-method static Proxy<Track> find(object|array|mixed $criteria)
 * @phpstan-method static Proxy<Track> findOrCreate(array $attributes)
 * @phpstan-method static Proxy<Track> first(string $sortedField = 'id')
 * @phpstan-method static Proxy<Track> last(string $sortedField = 'id')
 * @phpstan-method static Proxy<Track> random(array $attributes = [])
 * @phpstan-method static Proxy<Track> randomOrCreate(array $attributes = [])
 * @phpstan-method static RepositoryProxy<Track> repository()
 * @phpstan-method static list<Proxy<Track>> all()
 * @phpstan-method static list<Proxy<Track>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Proxy<Track>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Proxy<Track>> findBy(array $attributes)
 * @phpstan-method static list<Proxy<Track>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Proxy<Track>> randomSet(int $number, array $attributes = [])
 */
final class TrackFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'artist' => self::faker()->text(255),
            'name' => self::faker()->text(255),
            'playedAt' => self::faker()->dateTime(),
            'spotifyId' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Track $track): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Track::class;
    }
}
