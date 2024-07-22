<?php

declare(strict_types=1);

namespace Testcontainers\Wait;

interface WaitStrategy
{
    public function waitUntilReady(WaitStrategyTarget $waitStrategyTarget): void;

    public function withStartupTimeout(int $seconds): WaitStrategy;
}
