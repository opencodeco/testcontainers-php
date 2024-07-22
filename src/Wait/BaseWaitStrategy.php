<?php

declare(strict_types=1);

namespace Testcontainers\Wait;

use Testcontainers\Exception;

abstract class BaseWaitStrategy implements WaitStrategy
{
    private const DEFAULT_STARTUP_TIMEOUT_SECONDS = 60;

    private int $startupTimeout = self::DEFAULT_STARTUP_TIMEOUT_SECONDS;

    public function waitUntilReady(WaitStrategyTarget $waitStrategyTarget): void
    {
        $startTime = time();
        while (time() - $startTime < $this->startupTimeout) {
            try {
                if ($this->isContainerReady($waitStrategyTarget)) {
                    return;
                }
            } catch (\Exception $ex) {
                // ignore so that we can try again
            }

            usleep(100000);
        }

        throw new Exception('Timed out waiting for container started up');
    }

    public function withStartupTimeout(int $seconds): WaitStrategy
    {
        $this->startupTimeout = $seconds;
        return $this;
    }

    abstract protected function isContainerReady(WaitStrategyTarget $waitStrategyTarget): bool;
}
