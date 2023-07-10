<?php

namespace App\Worker\MessageHandler;

use App\Worker\Message\Track;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TrackHandler
{
    public function __invoke(Track $message)
    {
    }
}
