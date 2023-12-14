<?php

declare(strict_types=1);

namespace Testcontainers;

interface TestContainer
{
    public function start(): void;

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
}
