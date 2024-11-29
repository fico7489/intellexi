<?php

namespace App\ES\Connection;

class DefaultConnection
{
    public function getName(): string{
        return 'default';
    }

    public function getPrefix(): string{
        return 'prefix_';
    }

    public function getHost(): string
    {
        return 'elasticsearch';
    }

    public function getPort(): int{
        return 9200;
    }
}
