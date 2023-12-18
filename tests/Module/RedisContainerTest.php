<?php

declare(strict_types=1);

namespace Test\Testcontainers\Module;

use PHPUnit\Framework\TestCase;
use Predis\Client;
use Testcontainers\Module\Redis\RedisContainer;
use Testcontainers\TestContainer;
use Testcontainers\TestContainerException;

final class RedisContainerTest extends TestCase
{
    private static TestContainer $redisContainer;

    public static function setUpBeforeClass(): void
    {
        self::$redisContainer = new RedisContainer();
        self::$redisContainer->start();
    }

    public static function tearDownAfterClass(): void
    {
        self::$redisContainer->stop();
    }

    public function testConnectSetAndGet(): void
    {
        $redis_client = new Client([
            'host' => self::$redisContainer->getHost(),
            'port' => self::$redisContainer->getFirstMappedPort(),
        ]);

        $redis_client->set('testcontainers', 'php');

        self::assertSame('php', $redis_client->get('testcontainers'));
    }
}
