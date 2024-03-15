<?php

declare(strict_types=1);

namespace Testcontainers;

interface Runtime
{
    public function create(Container $container): self;

    public function start(Container $container): self;

    public function stop(Container $container): self;

    public function inspect(Container $container): Inspection;

    public function exec(Container $container, array $command): string;
}
