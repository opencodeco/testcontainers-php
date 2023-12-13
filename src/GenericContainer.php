<?php

declare(strict_types=1);

namespace Testcontainers;

class GenericContainer implements ContainerInterface
{
    public function start(): string
    {
        return 'Hello, World!';
    }
}
