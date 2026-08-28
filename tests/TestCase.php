<?php

declare(strict_types=1);

namespace Misaf\LaravelDockerEngine\Tests;

use Illuminate\Foundation\Application;
use Misaf\LaravelDockerEngine\Providers\ContainerServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

abstract class TestCase extends TestbenchTestCase
{
    /**
     * @param Application $app
     * @return list<class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [ContainerServiceProvider::class];
    }
}
