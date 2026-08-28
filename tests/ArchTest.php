<?php

declare(strict_types=1);

arch()->preset()->php();
arch()->preset()->security();
arch()->preset()->laravel();

arch('the package delegates Engine behavior to docker-engine-php')
    ->expect('Misaf\LaravelDockerEngine')
    ->not->toUse([
        'Illuminate\Http\Client',
        'Symfony\Component\Process',
    ]);

arch('production code only depends on Laravel, Package Tools, and the Engine SDK')
    ->expect('Misaf\LaravelDockerEngine')
    ->toOnlyUse([
        'Illuminate',
        'InvalidArgumentException',
        'LogicException',
        'Misaf\DockerEngine',
        'Misaf\LaravelDockerEngine',
        'Spatie\LaravelPackageTools',
        'Throwable',
    ]);
