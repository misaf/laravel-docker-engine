<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Container Driver
    |--------------------------------------------------------------------------
    |
    | The manager resolves this driver when no explicit driver is requested.
    | Driver clients remain lazy and are cached by Laravel's Manager class.
    |
    */

    'default' => env('CONTAINER_DRIVER', 'docker'),

    /*
    |--------------------------------------------------------------------------
    | Container Drivers
    |--------------------------------------------------------------------------
    |
    | Each driver is passed directly to docker-engine-php's ClientOptions
    | resolver. The SDK owns transport, TLS, timeouts, and API negotiation.
    |
    */

    'drivers' => [
        'docker' => [
            'host'        => env('DOCKER_HOST', 'unix:///var/run/docker.sock'),
            'api_version' => env('DOCKER_API_VERSION'),
            'timeouts'    => [
                'connect'     => (float) env('DOCKER_CONNECT_TIMEOUT', 5),
                'request'     => (float) env('DOCKER_REQUEST_TIMEOUT', 60),
                'stream_idle' => null === env('DOCKER_STREAM_IDLE_TIMEOUT')
                    ? null
                    : (float) env('DOCKER_STREAM_IDLE_TIMEOUT'),
            ],
            'tls'     => null,
            'headers' => [],
        ],

        'podman' => [
            'host'        => env('PODMAN_HOST', 'unix:///run/podman/podman.sock'),
            'api_version' => env('PODMAN_API_VERSION'),
            'timeouts'    => [
                'connect'     => (float) env('PODMAN_CONNECT_TIMEOUT', 5),
                'request'     => (float) env('PODMAN_REQUEST_TIMEOUT', 60),
                'stream_idle' => null === env('PODMAN_STREAM_IDLE_TIMEOUT')
                    ? null
                    : (float) env('PODMAN_STREAM_IDLE_TIMEOUT'),
            ],
            'tls'     => null,
            'headers' => [],
        ],
    ],

];
