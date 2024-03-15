<?php

declare(strict_types=1);

namespace Test\Testcontainers;

use PHPUnit\Framework\TestCase;
use Testcontainers\Container;
use Testcontainers\GenericContainer;

/**
 * @internal
 */
final class GenericContainerTest extends TestCase
{
    private static Container $container;

    public static function setUpBeforeClass(): void
    {
        self::$container = new GenericContainer('alpine');
        self::$container->withCommand(['tail', '-f', '/dev/null']);
        self::$container->start();
    }

    public static function tearDownAfterClass(): void
    {
        self::$container->stop();
    }

    public function testExec(): void
    {
        $actual = self::$container->exec(['echo', 'testcontainers']);
        self::assertSame('testcontainers', $actual);
    }
}
