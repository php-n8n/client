<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Fixtures;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

abstract class SimpleMessage implements MessageInterface
{
    /**
     * @var array<string, array{name: string, values: string[]}>
     */
    protected array $headers = [];

    protected string $protocolVersion = '1.1';

    protected StreamInterface $body;

    /**
     * @param array<string, string|string[]> $headers
     */
    public function __construct(array $headers = [], ?StreamInterface $body = null)
    {
        $this->body = $body ?? new SimpleStream();

        foreach ($headers as $name => $value) {
            $this->headers[strtolower($name)] = [
                'name' => $name,
                'values' => array_values(array_map('strval', (array) $value)),
            ];
        }
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion(string $version): static
    {
        $clone = clone $this;
        $clone->protocolVersion = $version;

        return $clone;
    }

    public function getHeaders(): array
    {
        $headers = [];

        foreach ($this->headers as $header) {
            $headers[$header['name']] = $header['values'];
        }

        return $headers;
    }

    public function hasHeader(string $name): bool
    {
        return array_key_exists(strtolower($name), $this->headers);
    }

    public function getHeader(string $name): array
    {
        return $this->headers[strtolower($name)]['values'] ?? [];
    }

    public function getHeaderLine(string $name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    public function withHeader(string $name, $value): static
    {
        $clone = clone $this;
        $clone->headers[strtolower($name)] = [
            'name' => $name,
            'values' => array_values(array_map('strval', (array) $value)),
        ];

        return $clone;
    }

    public function withAddedHeader(string $name, $value): static
    {
        $clone = clone $this;
        $key = strtolower($name);
        $values = array_values(array_map('strval', (array) $value));

        if (! isset($clone->headers[$key])) {
            $clone->headers[$key] = ['name' => $name, 'values' => $values];

            return $clone;
        }

        $clone->headers[$key]['values'] = array_merge($clone->headers[$key]['values'], $values);

        return $clone;
    }

    public function withoutHeader(string $name): static
    {
        $clone = clone $this;
        unset($clone->headers[strtolower($name)]);

        return $clone;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): static
    {
        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }
}
