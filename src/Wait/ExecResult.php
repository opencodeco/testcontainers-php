<?php

namespace Testcontainers\Wait;

final readonly class ExecResult
{
    public function __construct(
        public ?int $exitCode,
        public string $stdout,
    ) {
    }
}
