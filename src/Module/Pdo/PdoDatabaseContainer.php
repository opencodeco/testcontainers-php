<?php

declare(strict_types=1);

namespace Testcontainers\Module\Pdo;

use PDO;
use PDOException;
use Testcontainers\Exception;
use Testcontainers\GenericContainer;

abstract class PdoDatabaseContainer extends GenericContainer
{
    private const DEFAULT_STARTUP_TIMEOUT_SECONDS = 60;

    private int $startupTimeout = self::DEFAULT_STARTUP_TIMEOUT_SECONDS;

    public function getDsn(): string
    {
        return "{$this->getDriverName()}:host={$this->getHost()};port={$this->getFirstMappedPort()};dbname={$this->database}";
    }

    public function createPdo(): PDO
    {
        return new PDO($this->getDsn(), $this->username, $this->password);
    }

    public function withStartupTimeout(int $seconds): self
    {
        $this->startupTimeout = $seconds;
        return $this;
    }

    protected function waitUntilReady(): void
    {
        parent::waitUntilReady();

        if (extension_loaded('pdo')) {
            $startTime = time();
            while (time() - $startTime < $this->startupTimeout) {
                try {
                    $pdo = $this->createPdo();
                    $stmt = $pdo->query($this->getTestQuery());
                    if ($stmt->fetchColumn()) {
                        return;
                    }
                } catch (PDOException $ex) {
                    if ($ex->getMessage() === 'could not find driver') {
                        // we explicitly want this exception to fail fast without retries
                        throw $ex;
                    }
                    // ignore so that we can try again
                } catch (\Exception $ex) {
                    // ignore so that we can try again
                }

                usleep(100000);
            }

            throw new Exception('Timed out waiting for container started up');
        }
    }

    protected function getTestQuery(): string
    {
        return 'SELECT 1';
    }

    abstract protected function getDriverName(): string;
}
