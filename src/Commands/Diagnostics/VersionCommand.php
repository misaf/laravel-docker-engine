<?php

declare(strict_types=1);

namespace Misaf\LaravelDockerEngine\Commands\Diagnostics;

use Misaf\LaravelDockerEngine\Commands\ContainerCommand;

final class VersionCommand extends ContainerCommand
{
    protected $signature = 'container:version {--driver= : The configured container driver}';

    protected $description = 'Display version information for a configured container engine';

    public function handle(): int
    {
        $version = $this->driver()->system()->version();

        $this->table(['Property', 'Value'], [
            ['Engine version', $version->version],
            ['API version', $version->apiVersion],
            ['Minimum API version', $version->minimumApiVersion],
            ['Operating system', $version->operatingSystem],
            ['Architecture', $version->architecture],
        ]);

        return self::SUCCESS;
    }
}
