<?php

declare(strict_types=1);

namespace Testcontainers;

use Testcontainers\Wait\HostPortWaitingStrategy;
use Testcontainers\Wait\LogMessageWaitStrategy;
use Testcontainers\Wait\WaitStrategy;

class Wait
{
    public static function defaultWaitStrategy(): WaitStrategy
    {
        return self::forListeningPort();
    }

    public static function forListeningPort(int ...$ports): WaitStrategy
    {
        return new HostPortWaitingStrategy($ports);
    }

    public static function forLogMessage(string $pattern, bool $isRegex = false): WaitStrategy
    {
        return new LogMessageWaitStrategy($pattern, $isRegex);
    }
}
