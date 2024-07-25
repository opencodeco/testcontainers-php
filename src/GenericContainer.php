<?php

declare(strict_types=1);

namespace Testcontainers;

use Testcontainers\Wait\WaitStrategy;
use Testcontainers\Wait\WaitStrategyTarget;

class GenericContainer implements Container
{
    private string $id;

    /** @var array<string> */
    private array $exposedPorts = [];

    /** @var array<string> */
    private array $command = [];

    /** @var array<string> */
    private array $env = [];

    private WaitStrategy $waitStrategy;

    public function __construct(
        private readonly string $image,
        private ?Runtime $runtime = null,
    ) {
        $this->runtime ??= Testcontainers::getRuntime();
        $this->waitStrategy = Wait::defaultWaitStrategy();
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function start(): self
    {
        $this->runtime->create($this)->start($this);
        $this->waitUntilReady();
        return $this;
    }

    public function stop(): self
    {
        $this->runtime->stop($this);
        return $this;
    }

    public function inspect(): Inspection
    {
        return $this->runtime->inspect($this);
    }

    public function withExposedPorts(string $port): self
    {
        $this->exposedPorts[$port] = $port;
        return $this;
    }

    public function exec(array $command): string
    {
        return $this->runtime->exec($this, $command);
    }

    public function withCommand(array $command): self
    {
        $this->command = $command;
        return $this;
    }

    public function withEnv(array $env): self
    {
        $this->env = $env;
        return $this;
    }

    public function getHost(): string
    {
        $hostOverride = getenv('TESTCONTAINERS_HOST_OVERRIDE');
        if ($hostOverride !== false) {
            return $hostOverride;
        }

        if (!$this->insideContainer()) {
           return 'localhost';
        }

        return $this->inspect()->gateway;
    }

    private function insideContainer(): bool
    {
        return file_exists("/.dockerenv");
    }

    public function getMappedPort(string $port): int
    {
        return $this->inspect()->ports[$port];
    }

    public function getFirstMappedPort(): int
    {
        $port = array_key_first($this->getExposedPorts());
        return $this->getMappedPort($port);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function withId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getExposedPorts(): array
    {
        return $this->exposedPorts;
    }

    public function getCommand(): array
    {
        return $this->command;
    }

    public function getEnv(): array
    {
        return $this->env;
    }

    public function waitingFor(WaitStrategy $waitStrategy): self
    {
        $this->waitStrategy = $waitStrategy;
        return $this;
    }

    protected function waitUntilReady(): void
    {
        $this->waitStrategy?->waitUntilReady(new WaitStrategyTarget($this, $this->runtime));
    }
}
