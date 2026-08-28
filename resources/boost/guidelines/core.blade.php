## Laravel Docker Engine

`misaf/laravel-docker-engine` is a thin Laravel Manager integration over `misaf/docker-engine-php`.

- Inject `ContainerManager` and call `driver('docker')` or `driver('podman')` when selecting an Engine endpoint.
- Inject `DockerClient` when using the configured default SDK-backed driver.
- Use `Facades\Container` for Laravel facade ergonomics.
- Use SDK-owned resources, DTOs, value objects, streams, exceptions, raw API, TLS, negotiation, and capability detection directly.
- Register custom drivers with Laravel Manager's `extend()` and clear resolved drivers with `forgetDrivers()`.
- Never add Docker or Podman CLI calls, an HTTP transport, Engine schemas, or Laravel-specific copies of SDK domain models.
