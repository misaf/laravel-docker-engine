<?php

declare(strict_types=1);

namespace Misaf\LaravelDockerEngine\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;
use Misaf\DockerEngine\DockerClient;
use Misaf\LaravelDockerEngine\ContainerManager;

abstract class ContainerCommand extends Command
{
    public function __construct(protected readonly ContainerManager $containers)
    {
        parent::__construct();
    }

    protected function driver(): DockerClient
    {
        $driver = $this->option('driver');

        if (null !== $driver && ( ! is_string($driver) || '' === $driver)) {
            throw new InvalidArgumentException('The container driver option must be a non-empty string.');
        }

        $client = $this->containers->driver($driver);

        if ( ! $client instanceof DockerClient) {
            throw new InvalidArgumentException('Container commands require a docker-engine-php driver.');
        }

        return $client;
    }
}
