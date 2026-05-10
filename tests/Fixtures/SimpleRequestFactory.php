<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Fixtures;

use InvalidArgumentException;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

final class SimpleRequestFactory implements RequestFactoryInterface
{
    public function createRequest(string $method, $uri): RequestInterface
    {
        if (! $uri instanceof UriInterface) {
            throw new InvalidArgumentException('Test request factory expects a UriInterface instance.');
        }

        return new SimpleRequest($method, $uri);
    }
}
