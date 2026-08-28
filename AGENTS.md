# Repository Guidelines

## Project Structure & Module Organization

The Laravel integration package lives in `src/`. Package configuration is in `config/container.php`. Shared tests are split between `tests/Unit`, `tests/Feature`, and `tests/ArchTest.php`. Contributor-facing Laravel Boost resources live in `resources/boost/`.

## Build, Test, and Development Commands

Install PHP 8.4+ dependencies before development:

```bash
composer install
composer test
composer analyse
composer format
composer test-coverage
```

`composer test` runs the complete Pest suite, including driver and architecture tests. `composer analyse` runs PHPStan/Larastan with a 1 GB memory limit. `composer format` applies the Pint rules in `pint.json`; review its changes before committing. `composer test-coverage` generates a coverage report and requires a supported coverage driver such as Xdebug or PCOV.

## Coding Style & Naming Conventions

Use the PSR-4 namespace rooted at `Misaf\LaravelDockerEngine\`. Use four-space indentation, strict types, typed properties and returns, and ordered imports. Pint uses the `per` preset plus repository-specific rules, so run it rather than formatting by hand. Name classes in PascalCase, methods and variables in camelCase, and enums/value objects after their domain concept (for example, `ContainerState` and `ImageReference`). Keep runtime-specific behavior inside its driver; Engine API behavior belongs in `misaf/docker-engine-php`.

## Testing Guidelines

Tests use Pest 5 with Orchestra Testbench. Name files `*Test.php` and place behavioral integration tests in `Feature`, isolated domain tests in `Unit`, and dependency or layering rules in `ArchTest.php`. Prefer `FakeContainerRuntime` for daemon-free tests. Add coverage for both successful behavior and failure paths, then run `composer test` and `composer analyse` locally. No numeric coverage threshold is configured, but new behavior should be exercised directly.

## Commit & Pull Request Guidelines

The history currently establishes no formal convention beyond a concise imperative subject (`init`). Keep commits focused and use short, action-oriented subjects such as `Add Podman log validation`. Pull requests should explain the motivation and behavior change, list verification commands, and link relevant issues. Call out configuration or API compatibility changes; include terminal output for CLI presentation changes and update `README.md` or `CHANGELOG.md` when user-facing behavior changes.
