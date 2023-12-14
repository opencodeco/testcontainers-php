<?php

declare(strict_types=1);

namespace Testcontainers;

interface TestContainer
{
    /**
     * @return $this
     */
    public function run(): self;

    /**
     * @return $this
     */
    public function stop(): self;

    /**
     * @param array<string> $command
     * @return string
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
}
