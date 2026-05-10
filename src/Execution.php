<?php

declare(strict_types=1);

namespace PhpN8n\Client;

use PhpN8n\Client\Config\ExecutionFetchOptions;
use PhpN8n\Client\Config\PollingConfig;
use PhpN8n\Client\Contracts\ExecutionTrackerContract;
use PhpN8n\Client\Executions\ExecutionReference;
use PhpN8n\Client\Executions\ExecutionResult;

final readonly class Execution
{
    public function __construct(
        private ExecutionReference $reference,
        private ExecutionTrackerContract $tracker,
    ) {
    }

    public function reference(): ExecutionReference
    {
        return $this->reference;
    }

    public function refresh(?ExecutionFetchOptions $options = null): ExecutionResult
    {
        return $this->tracker->get($this->reference, $options);
    }

    public function wait(?PollingConfig $polling = null): ExecutionResult
    {
        return $this->tracker->wait($this->reference, $polling);
    }
}
