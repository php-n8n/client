<?php

declare(strict_types=1);

namespace PhpN8n\Client\Http;

use JsonException;
use Psr\Http\Message\ResponseInterface;

final readonly class ResponseDecoder
{
    public function body(ResponseInterface $response): string
    {
        $body = $response->getBody();

        if ($body->isSeekable()) {
            $body->rewind();
        }

        $contents = $body->getContents();

        if ($body->isSeekable()) {
            $body->rewind();
        }

        return $contents;
    }

    /**
     * @throws JsonException
     */
    public function json(ResponseInterface $response): mixed
    {
        $body = trim($this->body($response));

        if ($body === '') {
            return null;
        }

        return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    }

    public function isJson(ResponseInterface $response): bool
    {
        foreach ($response->getHeader('Content-Type') as $contentType) {
            if (str_contains(strtolower($contentType), 'json')) {
                return true;
            }
        }

        return false;
    }
}
