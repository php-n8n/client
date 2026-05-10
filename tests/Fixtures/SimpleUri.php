<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Fixtures;

use Psr\Http\Message\UriInterface;

final readonly class SimpleUri implements UriInterface
{
    public function __construct(private string $uri)
    {
    }

    public function getScheme(): string
    {
        return (string) (parse_url($this->uri, PHP_URL_SCHEME) ?? '');
    }

    public function getAuthority(): string
    {
        $host = $this->getHost();

        if ($host === '') {
            return '';
        }

        $port = $this->getPort();

        return $host . ($port === null ? '' : ':' . $port);
    }

    public function getUserInfo(): string
    {
        return '';
    }

    public function getHost(): string
    {
        return (string) (parse_url($this->uri, PHP_URL_HOST) ?? '');
    }

    public function getPort(): ?int
    {
        return parse_url($this->uri, PHP_URL_PORT) ?: null;
    }

    public function getPath(): string
    {
        return (string) (parse_url($this->uri, PHP_URL_PATH) ?? '');
    }

    public function getQuery(): string
    {
        return (string) (parse_url($this->uri, PHP_URL_QUERY) ?? '');
    }

    public function getFragment(): string
    {
        return (string) (parse_url($this->uri, PHP_URL_FRAGMENT) ?? '');
    }

    public function withScheme(string $scheme): UriInterface
    {
        return new self($this->build(['scheme' => $scheme]));
    }

    public function withUserInfo(string $user, ?string $password = null): UriInterface
    {
        return new self((string) $this);
    }

    public function withHost(string $host): UriInterface
    {
        return new self($this->build(['host' => $host]));
    }

    public function withPort(?int $port): UriInterface
    {
        return new self($this->build(['port' => $port]));
    }

    public function withPath(string $path): UriInterface
    {
        return new self($this->build(['path' => $path]));
    }

    public function withQuery(string $query): UriInterface
    {
        return new self($this->build(['query' => $query]));
    }

    public function withFragment(string $fragment): UriInterface
    {
        return new self($this->build(['fragment' => $fragment]));
    }

    public function __toString(): string
    {
        return $this->uri;
    }

    /**
     * @param array<string, mixed> $changes
     */
    private function build(array $changes): string
    {
        $scheme = $changes['scheme'] ?? $this->getScheme();
        $host = $changes['host'] ?? $this->getHost();
        $port = array_key_exists('port', $changes) ? $changes['port'] : $this->getPort();
        $path = $changes['path'] ?? $this->getPath();
        $query = $changes['query'] ?? $this->getQuery();
        $fragment = $changes['fragment'] ?? $this->getFragment();

        $uri = $scheme === '' ? '' : $scheme . '://';
        $uri .= $host;

        if ($port !== null) {
            $uri .= ':' . $port;
        }

        $uri .= $path === '' ? '' : (str_starts_with($path, '/') ? $path : '/' . $path);
        $uri .= $query === '' ? '' : '?' . $query;
        $uri .= $fragment === '' ? '' : '#' . $fragment;

        return $uri;
    }
}
