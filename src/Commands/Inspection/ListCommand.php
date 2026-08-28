<?php

declare(strict_types=1);

namespace Misaf\LaravelDockerEngine\Commands\Inspection;

use Misaf\LaravelDockerEngine\Commands\ContainerCommand;

final class ListCommand extends ContainerCommand
{
    protected $signature = 'container:list {--driver= : The configured container driver}';

    protected $description = 'List running containers on a configured container engine';

    public function handle(): int
    {
        $rows = [];

        foreach ($this->driver()->containers()->list() as $container) {
            $rows[] = [
                (string) $container->id,
                implode(', ', $container->names),
                $container->image,
                $container->state,
                $container->status,
            ];
        }

        $this->table(['ID', 'Names', 'Image', 'State', 'Status'], $rows);

        return self::SUCCESS;
    }
}
