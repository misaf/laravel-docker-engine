<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Misaf\DockerEngine\ApiVersion;
use Misaf\DockerEngine\DockerClient;
use Misaf\DockerEngine\Transport\Response;
use Misaf\LaravelDockerEngine\ContainerManager;
use Misaf\LaravelDockerEngine\Tests\Support\FakeTransport;

function fakeContainerDriver(string $name, Response ...$responses): FakeTransport
{
    $transport = new FakeTransport(...$responses);
    $client = new DockerClient($transport, ApiVersion::V1_55);

    app(ContainerManager::class)->extend($name, fn(): DockerClient => $client);

    return $transport;
}

it('registers the container convenience commands', function (): void {
    foreach (['ping', 'info', 'version', 'list', 'images'] as $command) {
        $this->artisan("container:{$command} --help")
            ->expectsOutputToContain("container:{$command}")
            ->assertSuccessful();
    }
});

it('pings the configured default driver through the SDK', function (): void {
    Config::set('container.default', 'testing');
    $transport = fakeContainerDriver('testing', new Response(200, [], 'OK'));

    $this->artisan('container:ping')
        ->expectsOutputToContain('OK')
        ->assertSuccessful();

    expect($transport->requests)->toHaveCount(1)
        ->and($transport->requests[0]->target())->toBe('/v1.55/_ping');
});

it('selects an explicit driver for engine information', function (): void {
    $transport = fakeContainerDriver('podman', new Response(200, [], json_encode([
        'ID'                => 'engine-id',
        'Name'              => 'podman.test',
        'Containers'        => 3,
        'ContainersRunning' => 2,
        'Images'            => 8,
        'OperatingSystem'   => 'linux',
        'Architecture'      => 'amd64',
    ], JSON_THROW_ON_ERROR)));

    $this->artisan('container:info', ['--driver' => 'podman'])
        ->expectsOutputToContain('podman.test')
        ->expectsOutputToContain('Running containers')
        ->assertSuccessful();

    expect($transport->requests[0]->target())->toBe('/v1.55/info');
});

it('displays engine version DTO fields', function (): void {
    $transport = fakeContainerDriver('docker', new Response(200, [], json_encode([
        'Version'       => '28.3.0',
        'ApiVersion'    => '1.55',
        'MinAPIVersion' => '1.24',
        'Os'            => 'linux',
        'Arch'          => 'arm64',
    ], JSON_THROW_ON_ERROR)));

    $this->artisan('container:version')
        ->expectsOutputToContain('28.3.0')
        ->expectsOutputToContain('1.55')
        ->assertSuccessful();

    expect($transport->requests[0]->target())->toBe('/v1.55/version');
});

it('lists containers through the selected driver', function (): void {
    $transport = fakeContainerDriver('podman', new Response(200, [], json_encode([[
        'Id'     => 'container-id',
        'Names'  => ['/web'],
        'Image'  => 'nginx:latest',
        'State'  => 'running',
        'Status' => 'Up 2 minutes',
    ]], JSON_THROW_ON_ERROR)));

    $this->artisan('container:list', ['--driver' => 'podman'])
        ->expectsOutputToContain('container-id')
        ->assertSuccessful();

    expect($transport->requests[0]->target())
        ->toBe('/v1.55/containers/json?all=false&size=false');
});

it('lists images through the selected driver', function (): void {
    $transport = fakeContainerDriver('docker', new Response(200, [], json_encode([[
        'Id'          => 'sha256:image-id',
        'RepoTags'    => ['app:latest'],
        'RepoDigests' => ['app@sha256:digest'],
        'Created'     => 1_700_000_000,
        'Size'        => 12_345,
    ]], JSON_THROW_ON_ERROR)));

    $this->artisan('container:images', ['--driver' => 'docker'])
        ->expectsOutputToContain('sha256:image-id')
        ->assertSuccessful();

    expect($transport->requests[0]->target())->toBe('/v1.55/images/json?all=false');
});
