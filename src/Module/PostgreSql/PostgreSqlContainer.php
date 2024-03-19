<?php

declare(strict_types=1);

namespace Testcontainers\Module\PostgreSql;

use PDO;
use Testcontainers\GenericContainer;

final class PostgreSqlContainer extends GenericContainer
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
            ]);
    }

    public function getDsn(): string
    {
        return "pgsql:host={$this->getHost()};port={$this->getFirstMappedPort()};dbname={$this->database}";
    }

    public function createPdo(): PDO
    {
        return new PDO($this->getDsn(), $this->username, $this->password);
    }

    public function start(int $wait = 15): self
    {
        return parent::start($wait); // TODO: Properly wait for PostgreSQL to be ready
    }
}
