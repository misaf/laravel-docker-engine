# Laravel Docker Engine

Laravel driver management for [`misaf/docker-engine-php`](https://github.com/misaf/docker-engine-php).
This package provides configuration, dependency injection, Laravel's native `Manager` pattern, and a facade. The SDK remains responsible for Docker Engine HTTP, transports, TLS, API negotiation, resources, schemas, streams, raw requests, and Docker/Podman compatibility.

## Requirements

- PHP 8.4+
- Laravel 13
- `misaf/docker-engine-php` 1.x

## Installation

```bash
composer require misaf/laravel-docker-engine
php artisan vendor:publish --tag=docker-engine-config
```

Laravel package discovery registers the service provider automatically. Registration and application boot do not contact Docker or Podman; a client is constructed only when its driver is first requested.

## Configuration

The published `config/container.php` selects a default driver and configures Docker-compatible Engine endpoints:

```php
return [
    'default' => env('CONTAINER_DRIVER', 'docker'),

    'drivers' => [
        'docker' => [
            'host' => env('DOCKER_HOST', 'unix:///var/run/docker.sock'),
            'api_version' => env('DOCKER_API_VERSION'),
            'timeouts' => [
                'connect' => 5,
                'request' => 60,
                'stream_idle' => null,
            ],
            'tls' => null,
            'headers' => [],
        ],

        'podman' => [
            'host' => env('PODMAN_HOST', 'unix:///run/podman/podman.sock'),
            'api_version' => env('PODMAN_API_VERSION'),
            'timeouts' => [
                'connect' => 5,
                'request' => 60,
                'stream_idle' => null,
            ],
            'tls' => null,
            'headers' => [],
        ],
    ],
];
```

Each driver array is passed directly to the SDK's `ClientOptions::resolve()`. Omit `api_version` to let the SDK negotiate it. For HTTPS/mTLS endpoints, use the SDK's TLS option shape:

```php
'docker' => [
    'host' => 'https://engine.example.test:2376',
    'api_version' => null,
    'tls' => [
        'ca' => '/etc/docker/ca.pem',
        'certificate' => '/etc/docker/cert.pem',
        'private_key' => '/etc/docker/key.pem',
        'private_key_password' => null,
        'verify_peer' => true,
        'verify_host' => true,
    ],
],
```

## Default usage

Unknown manager calls are proxied by Laravel's `Manager` to the default SDK driver:

```php
use Misaf\LaravelDockerEngine\Facades\Container;

$containers = Container::containers()->list();
$images = Container::images()->list();
```

All returned resource APIs, DTOs, value objects, streams, and exceptions belong to `misaf/docker-engine-php`.

## Docker driver

```php
Container::driver('docker')
    ->containers()
    ->list();
```

## Podman driver

Podman uses the SDK's Docker-compatible Engine API path; there is no duplicate Podman implementation in this package.

```php
Container::driver('podman')
    ->containers()
    ->list();
```

## Dependency injection

Inject the manager when selecting a driver:

```php
use Misaf\LaravelDockerEngine\ContainerManager;

final class ContainerService
{
    public function __construct(
        private readonly ContainerManager $containers,
    ) {}

    public function all(): array
    {
        return $this->containers
            ->driver('docker')
            ->containers()
            ->list();
    }
}
```

## Artisan commands

The package provides a small set of runtime-neutral commands for diagnostics and
inspection. They use the configured default driver unless `--driver` selects a
different entry from `container.drivers`:

```bash
php artisan container:ping
php artisan container:ping --driver=podman
php artisan container:info
php artisan container:version --driver=docker
php artisan container:list --driver=docker
php artisan container:images --driver=podman
```

Every command resolves `ContainerManager` from Laravel's service container and
calls the selected SDK-backed driver. The package never invokes Docker or Podman
CLI binaries; Engine API transport and response mapping remain owned by
`docker-engine-php`.

## Custom drivers

Custom creators use Laravel Manager's native `extend()` registry:

```php
use Misaf\LaravelDockerEngine\Facades\Container;

Container::extend('custom', function ($app) {
    return new CustomContainerClient();
});

Container::driver('custom')->containers()->list();
```

Laravel's manager resolves each driver lazily and caches it by name. Use `Container::forgetDrivers()` to discard all resolved driver instances. The package does not add database-style disconnect, purge, or reconnect methods because the SDK has no matching persistent-connection lifecycle.

## Development

Normal tests use SDK transport doubles and do not require a daemon:

```bash
composer test
composer analyse
composer format
composer test-coverage
```

Daemon-backed Docker and Podman compatibility tests belong to `misaf/docker-engine-php`.
