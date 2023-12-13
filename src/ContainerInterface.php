<?php

declare(strict_types=1);

namespace Testcontainers;

interface ContainerInterface
{
    public function start(): string;
}
