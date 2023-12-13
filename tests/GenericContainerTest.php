<?php

declare(strict_types=1);

namespace Test\Testcontainers;

use PHPUnit\Framework\TestCase;
use Testcontainers\GenericContainer;

final class GenericContainerTest extends TestCase
{
    public function testStart(): void
    {
        $container = new GenericContainer();
        $this->assertSame('Hello, World!', $container->start());
    }
}
