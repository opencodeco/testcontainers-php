<?php

declare(strict_types=1);

namespace Testcontainers\Wait;

use Testcontainers\Container;

final class HostPortWaitingStrategy extends BaseWaitStrategy
{
    /**
     * @param array<string> $ports
     */
    public function __construct(private readonly array $ports)
    {
    }

    protected function isContainerReady(WaitStrategyTarget $waitStrategyTarget): bool
    {
        return $this->checkInternalPorts($waitStrategyTarget) && $this->checkExternalPorts($waitStrategyTarget);
    }

    private function checkInternalPorts(WaitStrategyTarget $waitStrategyTarget): bool
    {
        $command[] = 'while true; do ( true';
        foreach ($this->getInternalPorts($waitStrategyTarget->container) as $port) {
            $command[] = '&&';
            $command[] = '(';
            $command[] = sprintf("grep -i ':0*%x' /proc/net/tcp*", $port);
            $command[] = '||';
            $command[] = sprintf('nc -vz -w 1 localhost %d', $port);
            $command[] = '||';
            $command[] = sprintf("/bin/bash -c '</dev/tcp/localhost/%d'", $port);
            $command[] = ')';
        }
        $command[] = ') && exit 0 || sleep 0.1; done';

        $execResult = $waitStrategyTarget->exec(['/bin/sh', '-c', implode(' ', $command)]);

        return $execResult->exitCode === 0;
    }

    /**
     * @return array<int>
     */
    private function getInternalPorts(Container $container): array
    {
        $internalPorts = [];
        foreach ($this->getPorts($container) as $port) {
            $internalPorts[] = intval(explode('/', $port)[0]);
        }

        return $internalPorts;
    }

    private function checkExternalPorts(WaitStrategyTarget $waitStrategyTarget): bool
    {
        $ready = true;

        $container = $waitStrategyTarget->container;
        $externalHost = $container->getHost();
        foreach ($this->getExternalPorts($container) as $port) {
            $ready = $ready && fsockopen($externalHost, $port, timeout: 1) !== false;
        }

        return $ready;
    }

    /**
     * @return array<int>
     */
    private function getExternalPorts(Container $container): array
    {
        $externalPorts = [];
        foreach ($this->getPorts($container) as $port) {
            $externalPorts[] = $container->getMappedPort($port);
        }

        return $externalPorts;
    }

    /**
     * @return array<string>
     */
    private function getPorts(Container $container): array
    {
        return ! empty($this->ports) ? $this->ports : $container->getExposedPorts();
    }
}
