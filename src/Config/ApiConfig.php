<?php

declare(strict_types=1);

namespace PhpN8n\Client\Config;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

final readonly class ApiConfig
{
    public function __construct(
        private UriInterface $apiUri,
        private ?string $apiKey = null,
    ) {
        if (trim((string) $apiUri) === '') {
            throw new InvalidArgumentException('The n8n API URI cannot be empty.');
        }
    }

    public static function fromUri(UriInterface $apiUri, ?string $apiKey = null): self
    {
        return new self($apiUri, $apiKey);
    }

    public function uri(): UriInterface
    {
        return $this->apiUri;
    }

    public function apiKey(): ?string
    {
        return $this->apiKey;
    }
}
