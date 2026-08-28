<?php

declare(strict_types=1);

namespace Misaf\LaravelDockerEngine\Providers;

use Illuminate\Contracts\Container\Container as Application;
use Misaf\LaravelDockerEngine\Commands\Diagnostics\InfoCommand;
use Misaf\LaravelDockerEngine\Commands\Diagnostics\PingCommand;
use Misaf\LaravelDockerEngine\Commands\Diagnostics\VersionCommand;
use Misaf\LaravelDockerEngine\Commands\Inspection\ImagesCommand;
use Misaf\LaravelDockerEngine\Commands\Inspection\ListCommand;
use Misaf\LaravelDockerEngine\ContainerManager;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class ContainerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-docker-engine')
            ->hasConfigFile('container')
            ->hasCommands([
                PingCommand::class,
                InfoCommand::class,
                VersionCommand::class,
                ListCommand::class,
                ImagesCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(
            ContainerManager::class,
            fn(Application $app): ContainerManager => new ContainerManager($app),
        );

        $this->app->alias(ContainerManager::class, 'container');
    }
}
