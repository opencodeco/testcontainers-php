<?php

declare(strict_types=1);

namespace Testcontainers\Module\MongoDb;

use MongoDB\Client;
use Testcontainers\Exception;
use Testcontainers\GenericContainer;

final class MongoDbContainer extends GenericContainer
{
    private const DEFAULT_STARTUP_TIMEOUT_SECONDS = 60;

    private int $startupTimeout = self::DEFAULT_STARTUP_TIMEOUT_SECONDS;

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

    public function getUri(): string
    {
        return "mongodb://{$this->username}:{$this->password}@{$this->getHost()}:{$this->getFirstMappedPort()}";
    }

    public function withStartupTimeout(int $seconds): self
    {
        $this->startupTimeout = $seconds;
        return $this;
    }

    protected function waitUntilReady(): void
    {
        parent::waitUntilReady();

        if (extension_loaded('mongodb')) {
            $client = new Client($this->getUri());

            $startTime = time();
            while (time() - $startTime < $this->startupTimeout) {
                try {
                    $iterator = $client->listDatabaseNames();
                    if ($iterator->valid()) {
                        return;
                    }
                } catch (\Exception $ex) {
                    // ignore so that we can try again
                }

                usleep(100000);
            }

            throw new Exception('Timed out waiting for container started up');
        }
    }
}
