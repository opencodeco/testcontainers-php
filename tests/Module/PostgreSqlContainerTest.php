<?php

declare(strict_types=1);

namespace Test\Testcontainers\Module;

use PHPUnit\Framework\TestCase;
use Testcontainers\Module\PostgreSql\PostgreSqlContainer;

/**
 * @internal
 * @requires extension pdo_pgsql
 */
final class PostgreSqlContainerTest extends TestCase
{
    private static PostgreSqlContainer $container;

    public static function setUpBeforeClass(): void
    {
        self::$container = new PostgreSqlContainer();
        self::$container->start();
    }

    protected function tearDown(): void
    {
        self::$container->stop();
    }

    public function testCRUD(): void
    {
        $pdo = self::$container->createPdo();

        self::assertNotFalse($pdo->exec('CREATE TABLE testcontainers (foo INT)'));
        self::assertNotFalse($pdo->exec('INSERT INTO testcontainers (foo) VALUES (1)'));

        $stmt = $pdo->query('SELECT foo FROM testcontainers');
        self::assertSame(1, $stmt->fetchColumn());

        self::assertNotFalse($pdo->exec('UPDATE testcontainers SET foo = 2 WHERE foo = 1'));
        $stmt = $pdo->query('SELECT foo FROM testcontainers');
        self::assertSame(2, $stmt->fetchColumn());

        self::assertNotFalse($pdo->exec('DELETE FROM testcontainers'));
    }
}
