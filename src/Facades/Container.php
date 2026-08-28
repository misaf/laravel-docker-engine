<?php

declare(strict_types=1);

namespace Misaf\LaravelDockerEngine\Facades;

use Illuminate\Support\Facades\Facade;
use Misaf\LaravelDockerEngine\ContainerManager;

/**
 * @method static \Misaf\DockerEngine\DockerClient driver(\UnitEnum|string|null $driver = null)
 * @method static ContainerManager extend(string $driver, \Closure $callback)
 * @method static array<string, mixed> getDrivers()
 * @method static ContainerManager forgetDrivers()
 * @method static \Misaf\DockerEngine\Contracts\Api\ContainerApi containers()
 * @method static \Misaf\DockerEngine\Contracts\Api\ImageApi images()
 * @method static \Misaf\DockerEngine\Contracts\Api\NetworkApi networks()
 * @method static \Misaf\DockerEngine\Contracts\Api\VolumeApi volumes()
 * @method static \Misaf\DockerEngine\Contracts\Api\ExecApi exec()
 * @method static \Misaf\DockerEngine\Contracts\Api\SystemApi system()
 * @method static \Misaf\DockerEngine\Raw\RawApi raw()
 * @method static \Misaf\DockerEngine\VersionedApi versioned()
 * @method static \Misaf\DockerEngine\Engine\EngineCapabilities capabilities()
 *
 * @see ContainerManager
 */
final class Container extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'container';
    }
}
