<?php

declare(strict_types=1);

namespace Misaf\LaravelDockerEngine\Commands\Diagnostics;

use Misaf\LaravelDockerEngine\Commands\ContainerCommand;

final class InfoCommand extends ContainerCommand
{
    protected $signature = 'container:info {--driver= : The configured container driver}';

    protected $description = 'Display information about a configured container engine';

    public function handle(): int
    {
        $info = $this->driver()->system()->info();

        $this->table(['Property', 'Value'], [
            ['ID', $info->id],
            ['Name', $info->name],
            ['Containers', (string) $info->containers],
            ['Running containers', (string) $info->containersRunning],
            ['Images', (string) $info->images],
            ['Operating system', $info->operatingSystem],
            ['Architecture', $info->architecture],
        ]);

        return self::SUCCESS;
    }
}
