<?php

declare(strict_types=1);

namespace PhpN8n\Client\Contracts;

use PhpN8n\Client\Hooks\HookContext;

interface HookRunnerContract
{
    public function run(HookContext $hook): void;
}
