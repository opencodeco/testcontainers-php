<?php

declare(strict_types=1);

namespace Testcontainers\Module\MongoDb;

use Testcontainers\GenericContainer;

final class MongoDbContainer extends GenericContainer
{
    public function __construct(
        string $image = 'mongo',
        public readonly string $username = 'test',
        public readonly string $password = 'test',
        public readonly string $database = 'test',
    ) {
        parent::__construct($image);

        $this
            ->withExposedPorts('27017/tcp')
            ->withEnv([
                "MONGO_INITDB_ROOT_USERNAME={$this->username}",
                "MONGO_INITDB_ROOT_PASSWORD={$this->password}",
                "MONGO_INITDB_DATABASE={$this->database}",
            ]);
    }

    public function start(int $wait = 15): self
    {
        return parent::start($wait); // TODO: Properly wait for MongoDB to be ready
    }

    public function getUri(): string
    {
        return "mongodb://{$this->username}:{$this->password}@{$this->getHost()}:{$this->getFirstMappedPort()}";
    }
}
