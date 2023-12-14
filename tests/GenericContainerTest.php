<?php

declare(strict_types=1);

namespace Test\Testcontainers;

use PHPUnit\Framework\TestCase;
use Testcontainers\TestContainer;
use Testcontainers\GenericTestContainer;
use Testcontainers\TestContainerException;

final class GenericContainerTest extends TestCase
{
    private static TestContainer $container;

    /**
     * @throws TestContainerException
     */
    public static function setUpBeforeClass(): void
    {
        self::$container = new GenericTestContainer('alpine');
        self::$container->withCommand(['tail', '-f', '/dev/null']);
        self::$container->start();
    }

    /**
     * @throws TestContainerException
     */
    public static function tearDownAfterClass(): void
    {
        self::$container->stop();
    }

    /**
     * @throws TestContainerException
     */
    public function testExec(): void
    {
        $expected = uniqid();
        $actual = self::$container->exec(['echo', $expected]);

        $this->assertSame($expected, trim($actual));
    }
}
