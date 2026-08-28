<?php

declare(strict_types=1);

namespace Misaf\LaravelDockerEngine\Commands\Diagnostics;

use Misaf\LaravelDockerEngine\Commands\ContainerCommand;

final class PingCommand extends ContainerCommand
{
    protected $signature = 'container:ping {--driver= : The configured container driver}';

    protected $description = 'Check connectivity to a configured container engine';

    public function handle(): int
    {
        $this->components->info($this->driver()->system()->ping());

        return self::SUCCESS;
    }
}
