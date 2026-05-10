<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Fixtures;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

final class SimpleRequest extends SimpleMessage implements RequestInterface
{
    private ?string $requestTarget = null;

    private UriInterface $uri;

    /**
     * @param array<string, string|string[]> $headers
     */
    public function __construct(
        private string $method,
        UriInterface $uri,
        array $headers = [],
        ?StreamInterface $body = null,
    ) {
        parent::__construct($headers, $body);
        $this->uri = $uri;
    }

    public function getRequestTarget(): string
    {
        return $this->requestTarget ?? ((string) $this->uri === '' ? '/' : (string) $this->uri);
    }

    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        $clone = clone $this;
        $clone->requestTarget = $requestTarget;

        return $clone;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod(string $method): RequestInterface
    {
        $clone = clone $this;
        $clone->method = $method;

        return $clone;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        $clone = clone $this;
        $clone->uri = $uri;

        return $clone;
    }
}
