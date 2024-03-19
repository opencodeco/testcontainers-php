<?php

declare(strict_types=1);

namespace Testcontainers\Module\RabbitMQ;

use Testcontainers\GenericContainer;

final class RabbitMQContainer extends GenericContainer
{
    public function __construct(
        string $image = 'rabbitmq',
        public readonly string $user = 'guest',
        public readonly string $pass = 'guest'
    ) {
        parent::__construct($image);

        $this
            ->withExposedPorts('5672/tcp')
            ->withEnv([
                'RABBITMQ_DEFAULT_USER' => $this->user,
                'RABBITMQ_DEFAULT_PASS' => $this->pass,
            ]);
    }
}
