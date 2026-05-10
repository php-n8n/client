<?php

declare(strict_types=1);

namespace PhpN8n\Client\Webhooks;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

final readonly class Webhook
{
    /**
     * @param array<string, string|string[]> $headers
     * @param array<string, mixed> $query
     */
    public function __construct(
        private UriInterface $url,
        private WebhookMethod $method = WebhookMethod::Post,
        private array $headers = [],
        private array $query = [],
    ) {
        $urlString = trim((string) $url);

        if ($urlString === '') {
            throw new InvalidArgumentException('Webhook URL cannot be empty.');
        }

        $scheme = strtolower($url->getScheme());

        if (! in_array($scheme, ['http', 'https'], true)) {
            throw new InvalidArgumentException('Webhook URL must use the http or https scheme.');
        }

        if ($url->getHost() === '') {
            throw new InvalidArgumentException('Webhook URL must include a host.');
        }

        if (filter_var($urlString, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Webhook URL is not a valid absolute URL.');
        }
    }

    public static function fromUri(UriInterface $url): self
    {
        return new self($url);
    }

    public function url(): UriInterface
    {
        return $this->url;
    }

    public function method(): WebhookMethod
    {
        return $this->method;
    }

    /**
     * @return array<string, string|string[]>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * @return array<string, mixed>
     */
    public function query(): array
    {
        return $this->query;
    }

    public function withMethod(WebhookMethod|string $method): self
    {
        return new self(
            $this->url,
            is_string($method) ? WebhookMethod::fromString($method) : $method,
            $this->headers,
            $this->query,
        );
    }

    /**
     * @param string|string[] $value
     */
    public function withHeader(string $name, string|array $value): self
    {
        return $this->withHeaders([$name => $value]);
    }

    /**
     * @param array<string, string|string[]> $headers
     */
    public function withHeaders(array $headers): self
    {
        return new self($this->url, $this->method, array_replace($this->headers, $headers), $this->query);
    }

    public function withQueryParameter(string $name, mixed $value): self
    {
        return $this->withQuery([$name => $value]);
    }

    /**
     * @param array<string, mixed> $query
     */
    public function withQuery(array $query): self
    {
        return new self($this->url, $this->method, $this->headers, array_replace($this->query, $query));
    }
}
