<?php

declare(strict_types=1);

namespace Misaf\LaravelDockerEngine\Commands\Inspection;

use Misaf\LaravelDockerEngine\Commands\ContainerCommand;

final class ImagesCommand extends ContainerCommand
{
    protected $signature = 'container:images {--driver= : The configured container driver}';

    protected $description = 'List images on a configured container engine';

    public function handle(): int
    {
        $rows = [];

        foreach ($this->driver()->images()->list() as $image) {
            $rows[] = [
                $image->id,
                implode(', ', $image->repoTags),
                (string) $image->created,
                (string) $image->size,
            ];
        }

        $this->table(['ID', 'Repository tags', 'Created', 'Size (bytes)'], $rows);

        return self::SUCCESS;
    }
}
