<?php

declare(strict_types=1);

namespace PhpN8n\Client\Executions;

use InvalidArgumentException;

final readonly class ExecutionReference
{
    private function __construct(private string $id)
    {
        if ($id === '') {
            throw new InvalidArgumentException('Execution reference ID cannot be empty.');
        }
    }

    public static function fromId(int|string $id): self
    {
        return new self(trim((string) $id));
    }

    public function id(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
