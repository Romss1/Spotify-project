<?php

namespace App\Worker\Message;

class Track
{
    public function __construct(private string $content)
    {
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
