<?php

declare(strict_types=1);

namespace Testcontainers;

use Docker\Docker;

final class Testcontainers
{
    private static Docker $runtime;

    public static function getRuntime(): Docker
    {
        if (! isset(self::$runtime)) {
            self::$runtime = Docker::create();
        }

        return self::$runtime;
    }
}
