<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Fixtures;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

final class SimpleStream implements StreamInterface
{
    private int $position = 0;

    public function __construct(private string $contents = '')
    {
    }

    public function __toString(): string
    {
        return $this->contents;
    }

    public function close(): void
    {
        $this->detach();
    }

    public function detach()
    {
        $this->contents = '';
        $this->position = 0;

        return null;
    }

    public function getSize(): ?int
    {
        return strlen($this->contents);
    }

    public function tell(): int
    {
        return $this->position;
    }

    public function eof(): bool
    {
        return $this->position >= strlen($this->contents);
    }

    public function isSeekable(): bool
    {
        return true;
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        $position = match ($whence) {
            SEEK_SET => $offset,
            SEEK_CUR => $this->position + $offset,
            SEEK_END => strlen($this->contents) + $offset,
            default => throw new RuntimeException('Invalid seek mode.'),
        };

        if ($position < 0) {
            throw new RuntimeException('Cannot seek before the beginning of the stream.');
        }

        $this->position = $position;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function isWritable(): bool
    {
        return true;
    }

    public function write(string $string): int
    {
        $before = substr($this->contents, 0, $this->position);
        $after = substr($this->contents, $this->position + strlen($string));
        $this->contents = $before . $string . $after;
        $this->position += strlen($string);

        return strlen($string);
    }

    public function isReadable(): bool
    {
        return true;
    }

    public function read(int $length): string
    {
        $chunk = substr($this->contents, $this->position, $length);
        $this->position += strlen($chunk);

        return $chunk;
    }

    public function getContents(): string
    {
        $contents = substr($this->contents, $this->position);
        $this->position = strlen($this->contents);

        return $contents;
    }

    public function getMetadata(?string $key = null): mixed
    {
        $metadata = ['seekable' => true];

        return $key === null ? $metadata : ($metadata[$key] ?? null);
    }
}
