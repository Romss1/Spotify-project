<?php

namespace App\Worker\Message;

class Track
{
    public function __construct(private readonly string $content)
    {
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
