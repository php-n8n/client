<?php

declare(strict_types=1);

namespace PhpN8n\Client\Hooks;

use PhpN8n\Client\Contracts\HookRunnerContract;

final readonly class NullHookRunner implements HookRunnerContract
{
    public function run(HookContext $hook): void
    {
    }
}
