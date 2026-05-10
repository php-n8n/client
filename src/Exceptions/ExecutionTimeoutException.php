<?php

declare(strict_types=1);

namespace PhpN8n\Client\Exceptions;

use PhpN8n\Client\Executions\ExecutionReference;

class ExecutionTimeoutException extends N8nException
{
    public function __construct(private readonly ExecutionReference $reference, int $timeoutSeconds)
    {
        parent::__construct("n8n execution {$reference->id()} did not finish within {$timeoutSeconds} seconds.");
    }

    public function reference(): ExecutionReference
    {
        return $this->reference;
    }
}
