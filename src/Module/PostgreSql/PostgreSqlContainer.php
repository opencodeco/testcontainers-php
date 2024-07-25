<?php

declare(strict_types=1);

namespace Testcontainers\Module\PostgreSql;

use Testcontainers\Module\Pdo\PdoDatabaseContainer;
use Testcontainers\Wait;

final class PostgreSqlContainer extends PdoDatabaseContainer
{
    public function __construct(
        string $image = 'postgres',
        public readonly string $username = 'test',
        public readonly string $password = 'test',
        public readonly string $database = 'test',
    ) {
        parent::__construct($image);

        $this
            ->withExposedPorts('5432/tcp')
            ->withEnv([
                "POSTGRES_USER={$this->username}",
                "POSTGRES_PASSWORD={$this->password}",
                "POSTGRES_DB={$this->database}",
            ])
            ->waitingFor(Wait::forLogMessage('database system is ready to accept connections'));
    }

    protected function getDriverName(): string
    {
        return 'pgsql';
    }
}
