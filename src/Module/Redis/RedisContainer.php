<?php

declare(strict_types=1);

namespace Testcontainers\Module\Redis;

use Testcontainers\GenericContainer;

final class RedisContainer extends GenericContainer
{
    protected array $exposedPorts = ['6379/tcp'];

    public function __construct(string $image = 'redis:alpine')
    {
        parent::__construct($image);
    }
}
