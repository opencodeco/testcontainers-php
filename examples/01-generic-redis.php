<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$redis_container = (new \Testcontainers\GenericContainer('redis:alpine'))
    ->withExposedPorts('6379/tcp')
    ->start();

$redis_client = new \Predis\Client([
    'host' => $redis_container->getHost(),
    'port' => $redis_container->getFirstMappedPort(),
]);

$redis_client->set('greetings', 'Hello, World!');
echo $redis_client->get('greetings');

$redis_container->stop();
