<?php

declare(strict_types=1);

namespace PhpN8n\Client\Executions;

final readonly class ExecutionData
{
    /**
     * @param array<string, mixed> $customData
     */
    public function __construct(private mixed $payload = null, private array $customData = [])
    {
    }

    public function payload(): mixed
    {
        return $this->payload;
    }

    /**
     * @return array<string, mixed>
     */
    public function customData(): array
    {
        return $this->customData;
    }
}
