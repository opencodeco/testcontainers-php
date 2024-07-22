<?php

declare(strict_types=1);

namespace Testcontainers\Wait;

use Docker\API\Model\ContainersIdExecPostBody;
use Docker\API\Runtime\Client\Client;
use Docker\Docker;
use Testcontainers\Container;
use Testcontainers\Runtime;

final class WaitStrategyTarget
{
    public function __construct(
        public readonly Container $container,
        private readonly Runtime $runtime,
    ) {
    }

    public function exec(array $command): ExecResult
    {
        $containerExec = (new ContainersIdExecPostBody())
            ->setCmd($command)
            ->setAttachStdout(true)
            ->setAttachStderr(true);

        $exec = $this->dockerClient()->containerExec($this->getContainerId(), $containerExec);

        $contents = $this->dockerClient()
            ->execStart($exec->getId(), fetch: Client::FETCH_RESPONSE)
            ->getBody()
            ->getContents();

        $execInspect = $this->dockerClient()->execInspect($exec->getId());

        return new ExecResult(
            $execInspect->getExitCode(),
            preg_replace('/[\x00-\x1F\x7F]/u', '', mb_convert_encoding($contents, 'UTF-8', 'UTF-8'))
        );
    }

    public function logs(): string
    {
        $contents = $this->dockerClient()
            ->containerLogs($this->getContainerId(), ['stdout' => true, 'stderr' => true], Client::FETCH_RESPONSE)
            ->getBody()
            ->getContents();

        return preg_replace('/[\x00-\x1F\x7F]/u', '', mb_convert_encoding($contents, 'UTF-8', 'UTF-8'));
    }

    private function getContainerId(): string
    {
        return $this->container->getId();
    }

    private function dockerClient(): Docker
    {
        return $this->runtime->dockerClient();
    }
}
