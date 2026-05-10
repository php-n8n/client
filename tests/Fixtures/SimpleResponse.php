<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Fixtures;

use Psr\Http\Message\ResponseInterface;

final class SimpleResponse extends SimpleMessage implements ResponseInterface
{
    /**
     * @param array<string, string|string[]> $headers
     */
    public function __construct(
        private int $statusCode = 200,
        private string $reasonPhrase = 'OK',
        string $body = '',
        array $headers = [],
    ) {
        parent::__construct($headers, new SimpleStream($body));
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
    {
        $clone = clone $this;
        $clone->statusCode = $code;
        $clone->reasonPhrase = $reasonPhrase;

        return $clone;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }
}
