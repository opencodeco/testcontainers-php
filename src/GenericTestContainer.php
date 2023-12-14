<?php

declare(strict_types=1);

namespace Testcontainers;

use Symfony\Component\Process\Process;

class GenericTestContainer implements TestContainer
{
    private string $name;

    /** @var array<string> */
    private array $command;

    public function __construct(
        private readonly string $image,
    ) {
        $this->name = uniqid('testcontainers-php_');
    }

    /**
     * @throws TestContainerException
     */
    public function start(): void
    {
        $process = new Process([
            'docker', 'run', '--rm', '-d',
            '--name', $this->name,
            $this->image,
            ...$this->command,
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw TestContainerException::start($process->getErrorOutput());
        }
    }

    /**
     * @throws TestContainerException
     */
    public function stop(): void
    {
        $process = new Process([
            'docker',
            'stop',
            $this->name,
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw TestContainerException::stop($this->name, $process->getErrorOutput());
        }
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
    public function withCommand(array $command): TestContainer
    {
        $this->command = $command;
        return $this;
    }
}
