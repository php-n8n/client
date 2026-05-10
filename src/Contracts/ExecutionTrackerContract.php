<?php

declare(strict_types=1);

namespace PhpN8n\Client\Contracts;

use PhpN8n\Client\Config\ExecutionFetchOptions;
use PhpN8n\Client\Config\PollingConfig;
use PhpN8n\Client\Executions\ExecutionReference;
use PhpN8n\Client\Executions\ExecutionResult;

interface ExecutionTrackerContract
{
    public function get(ExecutionReference $reference, ?ExecutionFetchOptions $options = null): ExecutionResult;

    public function wait(ExecutionReference $reference, ?PollingConfig $polling = null): ExecutionResult;
}
