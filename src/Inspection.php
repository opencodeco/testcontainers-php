<?php

declare(strict_types=1);

namespace Testcontainers;

final readonly class Inspection
{
    public function __construct(
        public string $gateway,
        /**
         * @var array<string, int>
         */
        public array $ports,
    ) {
    }
}
