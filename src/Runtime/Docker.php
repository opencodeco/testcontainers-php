<?php

declare(strict_types=1);

namespace Testcontainers\Runtime;

use Docker\API\Exception\ContainerCreateNotFoundException;
use Docker\API\Model\ContainerConfigExposedPortsItem;
use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\ContainersIdExecPostBody;
use Docker\API\Model\HostConfig;
use Docker\API\Model\PortBinding;
use Testcontainers\Container;
use Testcontainers\Inspection;
use Testcontainers\Runtime;

final class Docker implements Runtime
{
    public function __construct(
        private ?\Docker\Docker $docker = null,
    ) {
        $this->docker ??= \Docker\Docker::create();
    }

    public function create(Container $container): self
    {
        try {
            $hostConfig = new HostConfig();
            $body = new ContainersCreatePostBody();

            foreach ($container->getExposedPorts() as $port) {
                $body->setExposedPorts([$port => new ContainerConfigExposedPortsItem()]);
                $hostConfig->setPortBindings([$port => [new PortBinding()]]);
            }

            $body->setHostConfig($hostConfig);
            $body->setImage($container->getImage());
            $body->setCmd($container->getCommand());
            $body->setEnv($container->getEnv());

            $container->withId($this->docker->containerCreate($body)->getId());
        } catch (ContainerCreateNotFoundException) {
            $this->docker->imageCreate($container->getImage());
            return $this->create($container);
        }

        return $this;
    }

    public function start(Container $container): self
    {
        $this->docker->containerStart($container->getId());
        return $this;
    }

    public function stop(Container $container): self
    {
        $this->docker->containerStop($container->getId());
        $this->docker->containerDelete($container->getId());
        return $this;
    }

    public function inspect(Container $container): Inspection
    {
        $response = $this->docker->containerInspect($container->getId());
        $settings = $response->getNetworkSettings();

        $ports = [];
        foreach ($settings->getPorts() as $port => $value) {
            if ($value === null) {
                continue;
            }

            $ports[$port] = (int) $value[0]->getHostPort();
        }

        return new Inspection(
            gateway: $settings->getGateway(),
            ports: $ports,
        );
    }

    public function exec(Container $container, array $command): string
    {
        $containerExec = (new ContainersIdExecPostBody())
            ->setCmd($command)
            ->setAttachStdout(true)
            ->setAttachStderr(true);

        $exec = $this->docker->containerExec($container->getId(), $containerExec);

        $contents = $this->docker
            ->execStart($exec->getId(), fetch: 'response')
            ->getBody()
            ->getContents();

        return preg_replace('/[\x00-\x1F\x7F]/u', '', $contents);
    }
}
