<?php

declare(strict_types=1);

namespace PhpN8n\Client\Webhooks;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

final readonly class WebhookRequest
{
    private mixed $body;

    /**
     * @var array<string, string|string[]>
     */
    private array $headers;

    /**
     * @var array<string, mixed>
     */
    private array $query;

    private WebhookRequestBodyType $bodyType;

    /**
     * @param array<string, string|string[]> $headers
     * @param array<string, mixed> $query
     */
    public function __construct(
        mixed $body = null,
        array $headers = [],
        array $query = [],
        ?WebhookRequestBodyType $bodyType = null,
    ) {
        $this->body = $body;
        $this->headers = $headers;
        $this->query = $query;
        $this->bodyType = $bodyType ?? self::inferBodyType($body);

        self::assertBodyMatchesType($this->body, $this->bodyType);
    }

    public static function empty(): self
    {
        return new self(bodyType: WebhookRequestBodyType::Empty);
    }

    public static function json(mixed $body): self
    {
        return new self($body, bodyType: WebhookRequestBodyType::Json);
    }

    public static function text(string $body): self
    {
        return new self($body, bodyType: WebhookRequestBodyType::Text);
    }

    public static function stream(StreamInterface $body): self
    {
        return new self($body, bodyType: WebhookRequestBodyType::Stream);
    }

    public function body(): mixed
    {
        return $this->body;
    }

    public function bodyType(): WebhookRequestBodyType
    {
        return $this->bodyType;
    }

    public function streamBody(): StreamInterface
    {
        if (! $this->body instanceof StreamInterface) {
            throw new InvalidArgumentException('Webhook request body is not a stream.');
        }

        return $this->body;
    }

    public function textBody(): string
    {
        if (! is_string($this->body)) {
            throw new InvalidArgumentException('Webhook request body is not text.');
        }

        return $this->body;
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

    public function withoutBody(): self
    {
        return new self(null, $this->headers, $this->query, WebhookRequestBodyType::Empty);
    }

    public function withJsonBody(mixed $body): self
    {
        return new self($body, $this->headers, $this->query, WebhookRequestBodyType::Json);
    }

    public function withTextBody(string $body): self
    {
        return new self($body, $this->headers, $this->query, WebhookRequestBodyType::Text);
    }

    public function withStreamBody(StreamInterface $body): self
    {
        return new self($body, $this->headers, $this->query, WebhookRequestBodyType::Stream);
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
        return new self($this->body, array_replace($this->headers, $headers), $this->query, $this->bodyType);
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
        return new self($this->body, $this->headers, array_replace($this->query, $query), $this->bodyType);
    }

    private static function inferBodyType(mixed $body): WebhookRequestBodyType
    {
        if ($body === null) {
            return WebhookRequestBodyType::Empty;
        }

        if ($body instanceof StreamInterface) {
            return WebhookRequestBodyType::Stream;
        }

        if (is_string($body)) {
            return WebhookRequestBodyType::Text;
        }

        return WebhookRequestBodyType::Json;
    }

    private static function assertBodyMatchesType(mixed $body, WebhookRequestBodyType $bodyType): void
    {
        if ($bodyType === WebhookRequestBodyType::Empty && $body !== null) {
            throw new InvalidArgumentException('Empty webhook requests cannot contain a body.');
        }

        if ($bodyType === WebhookRequestBodyType::Text && ! is_string($body)) {
            throw new InvalidArgumentException('Text webhook request bodies must be strings.');
        }

        if ($bodyType === WebhookRequestBodyType::Stream && ! $body instanceof StreamInterface) {
            throw new InvalidArgumentException('Stream webhook request bodies must be PSR-7 streams.');
        }
    }
}
