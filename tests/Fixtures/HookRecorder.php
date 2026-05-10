<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Fixtures;

use PhpN8n\Client\Contracts\HookRunnerContract;
use PhpN8n\Client\Hooks\HookContext;
use PhpN8n\Client\Hooks\LifecycleHook;

final class HookRecorder implements HookRunnerContract
{
    /**
     * @var list<HookContext>
     */
    private array $contexts = [];

    public function run(HookContext $hook): void
    {
        $this->contexts[] = $hook;
    }

    /**
     * @return list<HookContext>
     */
    public function contexts(): array
    {
        return $this->contexts;
    }

    /**
     * @return list<LifecycleHook>
     */
    public function hooks(): array
    {
        return array_map(static fn (HookContext $context): LifecycleHook => $context->hook(), $this->contexts);
    }
}
