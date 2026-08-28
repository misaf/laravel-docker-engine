<?php

declare(strict_types=1);

namespace Misaf\LaravelDockerEngine;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Manager;
use Misaf\DockerEngine\Configuration\ClientOptions;
use Misaf\DockerEngine\DockerClient;

/** @mixin DockerClient */
final class ContainerManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return Config::string('container.default', 'docker');
    }

    protected function createDockerDriver(): DockerClient
    {
        return $this->createClient(Config::array('container.drivers.docker'));
    }

    protected function createPodmanDriver(): DockerClient
    {
        return $this->createClient(Config::array('container.drivers.podman'));
    }

    /** @param array<array-key, mixed> $config */
    private function createClient(array $config): DockerClient
    {
        return DockerClient::fromOptions(ClientOptions::resolve($config));
    }
}
