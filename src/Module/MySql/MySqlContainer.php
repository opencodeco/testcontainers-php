<?php

declare(strict_types=1);

namespace Testcontainers\Module\MySql;

use PDO;
use Testcontainers\GenericContainer;

final class MySqlContainer extends GenericContainer
{
    public function __construct(
        string $image = 'mysql',
        public readonly string $username = 'test',
        public readonly string $password = 'test',
        public readonly string $database = 'test',
    ) {
        parent::__construct($image);

        $this
            ->withExposedPorts('3306/tcp')
            ->withEnv([
                "MYSQL_USER={$this->username}",
                "MYSQL_PASSWORD={$this->password}",
                "MYSQL_DATABASE={$this->database}",
                'MYSQL_ROOT_PASSWORD=yes',
            ]);
    }

    public function getHost() : string
    {
        $host = parent::getHost();

        // depending on compile flags, mysql tries to connect on a local socket when
        // given 'localhost' as host so we need to correct it
        return $host == 'localhost' ? '127.0.0.1' : $host;
    }

    public function getDsn(): string
    {
        return "mysql:host={$this->getHost()};port={$this->getFirstMappedPort()};dbname={$this->database}";
    }

    public function createPdo(): PDO
    {
        return new PDO($this->getDsn(), $this->username, $this->password);
    }

    public function start(int $wait = 15): self
    {
        return parent::start($wait); // TODO: Properly wait for MySQL to be ready
    }
}
