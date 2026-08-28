---
name: laravel-docker-engine-development
description: Develop Laravel integrations over misaf/docker-engine-php using Laravel Manager drivers.
---

# Laravel Docker Engine Development

Treat this package as Laravel integration only. Keep Engine API calls, transports, schemas, DTOs, streaming, TLS, version negotiation, raw access, and Docker/Podman differences in `misaf/docker-engine-php`.

Use `ContainerManager::driver()` for Docker, Podman, and application-defined drivers. Rely on `Illuminate\Support\Manager` for lazy resolution, caching, custom creators, default selection, and forgetting resolved drivers. Return SDK objects directly from built-in drivers and do not introduce separate Docker or Podman Engine implementations.
