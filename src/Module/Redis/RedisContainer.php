<?php

declare(strict_types=1);

namespace Testcontainers\Module\Redis;

use Testcontainers\GenericContainer;
use Testcontainers\Wait;

final class RedisContainer extends GenericContainer
{
    public function __construct(string $image = 'redis:alpine')
    {
        parent::__construct($image);

        $this->withExposedPorts('6379/tcp')
            ->waitingFor(Wait::forLogMessage('Ready to accept connections'));
    }
}
