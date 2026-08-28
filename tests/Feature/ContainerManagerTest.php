<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Manager;
use Illuminate\Support\ServiceProvider;
use Misaf\DockerEngine\ApiVersion;
use Misaf\DockerEngine\DockerClient;
use Misaf\DockerEngine\Transport\Response;
use Misaf\LaravelDockerEngine\ContainerManager;
use Misaf\LaravelDockerEngine\Facades\Container;
use Misaf\LaravelDockerEngine\Tests\Support\FakeTransport;

beforeEach(function (): void {
    Config::set('container.default', 'docker');
    Config::set('container.drivers.docker.api_version', '1.55');
    Config::set('container.drivers.podman.api_version', '1.55');
});

it('extends Laravel Manager and resolves the configured default driver', function (): void {
    $manager = app(ContainerManager::class);

    expect($manager)->toBeInstanceOf(Manager::class)
        ->and($manager->getDefaultDriver())->toBe('docker')
        ->and($manager->driver())->toBeInstanceOf(DockerClient::class)
        ->and($manager->driver()->version())->toBe(ApiVersion::V1_55);
});

it('resolves Docker and Podman drivers independently', function (): void {
    $manager = app(ContainerManager::class);
    $docker = $manager->driver('docker');
    $podman = $manager->driver('podman');

    expect($docker)->toBeInstanceOf(DockerClient::class)
        ->and($podman)->toBeInstanceOf(DockerClient::class)
        ->and($podman)->not->toBe($docker)
        ->and($manager->getDrivers())->toHaveKeys(['docker', 'podman']);
});

it('caches drivers and forgets every resolved driver through Laravel Manager', function (): void {
    $manager = app(ContainerManager::class);
    $docker = $manager->driver('docker');
    $podman = $manager->driver('podman');

    expect($manager->driver('docker'))->toBe($docker)
        ->and($manager->driver('podman'))->toBe($podman)
        ->and($manager->forgetDrivers())->toBe($manager)
        ->and($manager->getDrivers())->toBeEmpty()
        ->and($manager->driver('docker'))->not->toBe($docker);
});

it('uses Laravel custom driver creators registered through extend', function (): void {
    $custom = new stdClass();
    $manager = app(ContainerManager::class);

    $result = $manager->extend('custom', function ($app) use ($custom): stdClass {
        expect($app)->toBe(app());

        return $custom;
    });

    expect($result)->toBe($manager)
        ->and($manager->driver('custom'))->toBe($custom)
        ->and($manager->driver('custom'))->toBe($custom);
});

it('merges the default Docker and Podman configuration without resolving clients', function (): void {
    expect(config('container.default'))->toBe('docker')
        ->and(config('container.drivers.docker.host'))->toBe('unix:///var/run/docker.sock')
        ->and(config('container.drivers.podman.host'))->toBe('unix:///run/podman/podman.sock')
        ->and(app()->resolved(ContainerManager::class))->toBeFalse();

    $manager = app(ContainerManager::class);

    expect($manager->getDrivers())->toBeEmpty();
});

it('maps custom Docker and Podman sockets through SDK client options', function (): void {
    Config::set('container.drivers.docker.host', 'unix:///tmp/custom-docker.sock');
    Config::set('container.drivers.podman.host', 'unix:///tmp/custom-podman.sock');

    $manager = app(ContainerManager::class);

    expect($manager->driver('docker'))->toBeInstanceOf(DockerClient::class)
        ->and($manager->driver('podman'))->toBeInstanceOf(DockerClient::class);
});

it('maps API version, timeout, header, and TLS configuration through SDK client options', function (): void {
    Config::set('container.drivers.docker', [
        'host'        => 'https://engine.example.test:2376',
        'api_version' => '1.50',
        'timeouts'    => [
            'connect'     => 2,
            'request'     => 30,
            'stream_idle' => 120,
        ],
        'tls' => [
            'ca'                   => '/certs/ca.pem',
            'certificate'          => '/certs/cert.pem',
            'private_key'          => '/certs/key.pem',
            'private_key_password' => 'secret',
            'verify_peer'          => true,
            'verify_host'          => true,
        ],
        'headers' => ['X-Engine-Scope' => 'production'],
    ]);

    expect(app(ContainerManager::class)->driver('docker')->version())->toBe(ApiVersion::V1_50);
});

it('rejects unsupported, missing, and invalid driver configuration', function (): void {
    $manager = app(ContainerManager::class);

    expect(fn(): mixed => $manager->driver('missing'))
        ->toThrow(InvalidArgumentException::class, 'Driver [missing] not supported.');

    Config::set('container.drivers.docker', null);

    expect(fn(): mixed => $manager->driver('docker'))
        ->toThrow(InvalidArgumentException::class, 'Configuration value for key [container.drivers.docker] must be an array');

    Config::set('container.drivers.podman.host', 'ftp://invalid.example.test');

    expect(fn(): mixed => $manager->driver('podman'))
        ->toThrow(InvalidArgumentException::class, 'Engine host must use');
});

it('rejects a non-string default driver name', function (mixed $driver): void {
    Config::set('container.default', $driver);

    expect(fn(): string => app(ContainerManager::class)->getDefaultDriver())
        ->toThrow(InvalidArgumentException::class, 'Configuration value for key [container.default] must be a string');
})->with([null, false]);

it('registers the manager singleton and short alias', function (): void {
    $manager = app(ContainerManager::class);
    $injected = app()->call(
        fn(ContainerManager $containers): ContainerManager => $containers,
    );

    expect(app('container'))->toBe($manager)
        ->and(app(ContainerManager::class))->toBe($manager)
        ->and($injected)->toBe($manager)
        ->and(app()->bound(DockerClient::class))->toBeFalse();
});

it('resolves the facade and proxies resource calls to the default SDK driver', function (): void {
    $transport = new FakeTransport(new Response(200, [], '[]'));
    $client = new DockerClient($transport, ApiVersion::V1_55);
    app(ContainerManager::class)->extend('docker', fn(): DockerClient => $client);

    expect(Container::getFacadeRoot())->toBe(app('container'))
        ->and(Container::driver('docker'))->toBe($client)
        ->and(Container::containers()->list())->toBe([])
        ->and($transport->requests[0]->target())->toBe('/v1.55/containers/json?all=false&size=false');
});

it('publishes the configuration under the package config tag', function (): void {
    $paths = ServiceProvider::pathsToPublish(
        Misaf\LaravelDockerEngine\Providers\ContainerServiceProvider::class,
        'docker-engine-config',
    );

    expect($paths)->toHaveCount(1)
        ->and(array_values($paths)[0])->toBe(config_path('container.php'));
});
