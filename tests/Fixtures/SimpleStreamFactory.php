<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Fixtures;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final class SimpleStreamFactory implements StreamFactoryInterface
{
    public function createStream(string $content = ''): StreamInterface
    {
        return new SimpleStream($content);
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return new SimpleStream((string) file_get_contents($filename));
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        return new SimpleStream((string) stream_get_contents($resource));
    }
}
