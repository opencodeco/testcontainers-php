<?php

declare(strict_types=1);

namespace Testcontainers\Module\Redis;

use Testcontainers\GenericContainer;

final class RedisContainer extends GenericContainer
{
    public function __construct(string $image = 'redis:alpine')
    {
        parent::__construct($image);
        $this->withExposedPorts('6379/tcp');
    }
}
