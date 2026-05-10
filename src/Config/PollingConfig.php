<?php

declare(strict_types=1);

namespace PhpN8n\Client\Config;

use InvalidArgumentException;

final readonly class PollingConfig
{
    public function __construct(
        private int $timeoutSeconds = 60,
        private int $intervalMilliseconds = 1000,
        private ?ExecutionFetchOptions $fetchOptions = null,
    ) {
        if ($timeoutSeconds < 0) {
            throw new InvalidArgumentException('Polling timeout must be zero or greater.');
        }

        if ($intervalMilliseconds < 0) {
            throw new InvalidArgumentException('Polling interval must be zero or greater.');
        }
    }

    public static function default(): self
    {
        return new self();
    }

    public function timeoutSeconds(): int
    {
        return $this->timeoutSeconds;
    }

    public function intervalMilliseconds(): int
    {
        return $this->intervalMilliseconds;
    }

    public function fetchOptions(): ExecutionFetchOptions
    {
        return $this->fetchOptions ?? ExecutionFetchOptions::default();
    }
}
