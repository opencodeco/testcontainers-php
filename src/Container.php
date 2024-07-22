<?php

declare(strict_types=1);

namespace Testcontainers;

use Testcontainers\Wait\WaitStrategy;

interface Container
{
    /**
     * @return $this
     */
    public function start(): self;

    /**
     * @return $this
     */
    public function stop(): self;

    /**
     * @param array<string> $command
     */
    public function exec(array $command): string;

    /**
     * @param array<string> $command
     * @return $this
     */
    public function withCommand(array $command): self;

    public function getHost(): string;

    public function getMappedPort(string $port): int;

    public function getFirstMappedPort(): int;

    /**
     * @return $this
     */
    public function withExposedPorts(string $port): self;

    public function getImage(): string;

    public function getId(): string;

    public function withId(string $id): self;

    /**
     * @return array<string>
     */
    public function getExposedPorts(): array;

    /**
     * @return array<string>
     */
    public function getCommand(): array;

    /**
     * @return array<string>
     */
    public function getEnv(): array;

    public function waitingFor(WaitStrategy $waitStrategy): self;
}
