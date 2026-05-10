<?php

declare(strict_types=1);

namespace PhpN8n\Client\Webhooks;

use InvalidArgumentException;

enum WebhookMethod: string
{
    case Delete = 'DELETE';
    case Get = 'GET';
    case Head = 'HEAD';
    case Options = 'OPTIONS';
    case Patch = 'PATCH';
    case Post = 'POST';
    case Put = 'PUT';

    public static function fromString(string $method): self
    {
        return self::tryFrom(strtoupper($method))
            ?? throw new InvalidArgumentException("Unsupported webhook HTTP method [{$method}].");
    }
}
