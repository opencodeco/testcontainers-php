<?php

declare(strict_types=1);

namespace Testcontainers;

use Symfony\Component\Process\Process;

class GenericContainer implements TestContainer
{
    /** @var array<string> */
    protected array $exposedPorts = [];

    private string $name;

    /** @var array<string> */
    private array $command = [];

    private array $inspectMemoize;

    public function __construct(
        private readonly string $image,
    ) {
        $this->name = uniqid('testcontainers-php_');
    }

    /**
     * @throws TestContainerException
     */
    public function run(): self
    {
        $process = new Process([
            'docker', 'run', '--rm', '-d',
            '--name', $this->name,
            ...$this->exposePorts(),
            $this->image,
            ...$this->command,
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw TestContainerException::start($process->getErrorOutput());
        }

        return $this;
    }

    /**
     * @throws \JsonException
     */
    public function inspect(): array
    {
        if (isset($this->inspectMemoize)) {
            return $this->inspectMemoize;
        }

        $process = new Process([
            'docker', 'inspect',
            $this->name,
        ]);

        $process->run();

        return $this->inspectMemoize = \json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * @inheritDoc
     * @throws TestContainerException
     */
    public function stop(): self
    {
        $process = new Process([
            'docker', 'stop',
            $this->name,
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw TestContainerException::stop($this->name, $process->getErrorOutput());
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws TestContainerException
     */
    public function exec(array $command): string
    {
        $process = new Process([
            'docker', 'exec',
            $this->name,
            ...$command,
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw TestContainerException::exec($this->name, $process->getErrorOutput());
        }

        return $process->getOutput();
    }

    /**
     * @inheritDoc
     */
    public function withCommand(array $command): self
    {
        $this->command = $command;
        return $this;
    }

    public function getHost(): string
    {
        return $this->inspect()[0]['NetworkSettings']['Gateway'];
    }

    /**
     * @throws \JsonException
     */
    public function getMappedPort(string $port): int
    {
        return (int) $this->inspect()[0]['NetworkSettings']['Ports'][$port][0]['HostPort'];
    }

    public function withExposedPorts(string $port): self
    {
        $this->exposedPorts[] = $port;
        return $this;
    }

    private function exposePorts(): array
    {
        $ports = [];

        foreach ($this->exposedPorts as $port) {
            $ports[] = "-p$port";
        }

        return $ports;
    }

    public function getFirstMappedPort(): int
    {
        return $this->getMappedPort($this->exposedPorts[0]);
    }
}
