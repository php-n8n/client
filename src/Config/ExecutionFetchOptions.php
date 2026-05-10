<?php

declare(strict_types=1);

namespace PhpN8n\Client\Config;

final readonly class ExecutionFetchOptions
{
    public function __construct(
        private bool $includeData = false,
        private ?bool $redactExecutionData = null,
    ) {
    }

    public static function default(): self
    {
        return new self();
    }

    public static function withData(?bool $redactExecutionData = null): self
    {
        return new self(true, $redactExecutionData);
    }

    public function includeData(): bool
    {
        return $this->includeData;
    }

    public function redactExecutionData(): ?bool
    {
        return $this->redactExecutionData;
    }

    /**
     * @return array<string, string>
     */
    public function toQuery(): array
    {
        $query = ['includeData' => $this->includeData ? 'true' : 'false'];

        if ($this->redactExecutionData !== null) {
            $query['redactExecutionData'] = $this->redactExecutionData ? 'true' : 'false';
        }

        return $query;
    }
}
