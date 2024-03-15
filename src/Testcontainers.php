<?php

declare(strict_types=1);

namespace Testcontainers;

use Testcontainers\Runtime\Docker;

final class Testcontainers
{
    private static Runtime $runtime;

    public static function getRuntime(string $default = Docker::class): Runtime
    {
        if (! isset(self::$runtime)) {
            return self::setRuntime(new $default());
        }

        return self::$runtime;
    }

    public static function setRuntime(Runtime $runtime): Runtime
    {
        return self::$runtime = $runtime;
    }
}
