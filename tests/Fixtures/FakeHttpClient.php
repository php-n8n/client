<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Fixtures;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

final class FakeHttpClient implements ClientInterface
{
    /**
     * @var list<RequestInterface>
     */
    private array $requests = [];

    /**
     * @var list<ResponseInterface>
     */
    private array $responses = [];

    public function queueResponse(ResponseInterface $response): self
    {
        $this->responses[] = $response;

        return $this;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->requests[] = $request;

        return array_shift($this->responses) ?? throw new RuntimeException('No fake response queued.');
    }

    /**
     * @return list<RequestInterface>
     */
    public function requests(): array
    {
        return $this->requests;
    }

    public function lastRequest(): RequestInterface
    {
        return $this->requests[array_key_last($this->requests)] ?? throw new RuntimeException('No request was sent.');
    }
}
