<?php

declare(strict_types=1);

namespace PhpN8n\Client\Http;

use JsonException;
use PhpN8n\Client\Exceptions\RequestException;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriInterface;

final readonly class RequestFactory
{
    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
    ) {
    }

    /**
     * @param array<string, string|string[]> $headers
     * @param array<string, mixed> $query
     */
    public function createEmpty(
        string $method,
        UriInterface $uri,
        array $headers = [],
        array $query = [],
    ): RequestInterface {
        return $this->withHeaders(
            $this->requestFactory->createRequest(strtoupper($method), $this->appendQuery($uri, $query)),
            $headers,
        );
    }

    /**
     * @param array<string, string|string[]> $headers
     * @param array<string, mixed> $query
     */
    public function createJson(
        string $method,
        UriInterface $uri,
        mixed $body,
        array $headers = [],
        array $query = [],
    ): RequestInterface {
        try {
            $payload = json_encode($body, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RequestException('Unable to encode request body as JSON.', previous: $exception);
        }

        $request = $this->createEmpty($method, $uri, $headers, $query)
            ->withBody($this->streamFactory->createStream($payload));

        if (! $request->hasHeader('Content-Type')) {
            $request = $request->withHeader('Content-Type', 'application/json');
        }

        return $request;
    }

    /**
     * @param array<string, string|string[]> $headers
     * @param array<string, mixed> $query
     */
    public function createText(
        string $method,
        UriInterface $uri,
        string $body,
        array $headers = [],
        array $query = [],
    ): RequestInterface {
        $request = $this->createEmpty($method, $uri, $headers, $query)
            ->withBody($this->streamFactory->createStream($body));

        if (! $request->hasHeader('Content-Type')) {
            $request = $request->withHeader('Content-Type', 'text/plain');
        }

        return $request;
    }

    /**
     * @param array<string, string|string[]> $headers
     * @param array<string, mixed> $query
     */
    public function createStream(
        string $method,
        UriInterface $uri,
        StreamInterface $body,
        array $headers = [],
        array $query = [],
    ): RequestInterface {
        return $this->createEmpty($method, $uri, $headers, $query)->withBody($body);
    }

    /**
     * @param array<string, string|string[]> $headers
     */
    private function withHeaders(RequestInterface $request, array $headers): RequestInterface
    {
        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        return $request;
    }

    /**
     * @param array<string, mixed> $query
     */
    private function appendQuery(UriInterface $uri, array $query): UriInterface
    {
        if ($query === []) {
            return $uri;
        }

        $queryString = http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        if ($queryString === '') {
            return $uri;
        }

        $currentQuery = $uri->getQuery();

        return $uri->withQuery($currentQuery === '' ? $queryString : $currentQuery . '&' . $queryString);
    }
}
