<?php

declare(strict_types=1);

namespace Misaf\LaravelDockerEngine\Tests\Support;

use LogicException;
use Misaf\DockerEngine\Contracts\Transport;
use Misaf\DockerEngine\Transport\Request;
use Misaf\DockerEngine\Transport\Response;
use Misaf\DockerEngine\Transport\StreamResponse;

final class FakeTransport implements Transport
{
    /** @var list<Request> */
    public array $requests = [];

    /** @var list<Response> */
    private array $responses;

    public function __construct(Response ...$responses)
    {
        $this->responses = $responses;
    }

    public function request(Request $request): Response
    {
        $this->requests[] = $request;

        return array_shift($this->responses)
            ?? throw new LogicException('No fake response was queued.');
    }

    public function stream(Request $request): StreamResponse
    {
        throw new LogicException('No fake stream response was queued.');
    }
}
