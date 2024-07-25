<?php

declare(strict_types=1);

namespace Testcontainers\Wait;

class LogMessageWaitStrategy extends BaseWaitStrategy
{
    public function __construct(
        private readonly string $pattern,
        private readonly bool $isRegex = false,
    ) {
    }

    protected function isContainerReady(WaitStrategyTarget $waitStrategyTarget): bool
    {
        $logs = $waitStrategyTarget->logs();

        return $this->isRegex
            ? preg_match($this->pattern, $logs) === 1
            : str_contains($logs, $this->pattern);
    }
}
