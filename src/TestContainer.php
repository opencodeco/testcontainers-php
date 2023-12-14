<?php

declare(strict_types=1);

namespace Testcontainers;

interface TestContainer
{
    public function run(): void;

    public function stop(): void;

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

    public function withExposedPorts(string $port): self;
}
