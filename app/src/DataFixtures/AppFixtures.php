<?php

namespace App\DataFixtures;

use App\Factory\TrackFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; ++$i) {
            TrackFactory::createMany(10, [
                'user' => UserFactory::createOne(),
            ]);
        }

        $manager->flush();
    }
}
