<?php

declare(strict_types=1);

namespace Testcontainers;

final class TestContainerException extends \Exception
{
    public static function start(string $errorMessage): self
    {
        return new self(sprintf('Failed to start container: %s', $errorMessage));
    }

    public static function stop(string $containerName, string $errorMessage): self
    {
        return new self(sprintf('Failed to stop container (%s): %s', $containerName, $errorMessage));
    }

    public static function exec(string $name, string $errorMessage): self
    {
        return new self(sprintf('Failed to exec command in container (%s): %s', $name, $errorMessage));
    }
}
