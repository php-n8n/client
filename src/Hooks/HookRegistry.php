<?php

declare(strict_types=1);

namespace PhpN8n\Client\Hooks;

use Closure;
use PhpN8n\Client\Contracts\HookRunnerContract;

final readonly class HookRegistry implements HookRunnerContract
{
    /**
     * @var array<string, list<Closure(HookContext): void>>
     */
    private array $callbacks;

    /**
     * @param array<string, list<Closure(HookContext): void>> $callbacks
     */
    public function __construct(array $callbacks = [])
    {
        $this->callbacks = $callbacks;
    }

    /**
     * @param Closure(HookContext): void $callback
     */
    public function on(LifecycleHook $hook, Closure $callback): self
    {
        $callbacks = $this->callbacks;
        $callbacks[$hook->value][] = $callback;

        return new self($callbacks);
    }

    public function run(HookContext $hook): void
    {
        foreach ($this->callbacks[$hook->hook()->value] ?? [] as $callback) {
            $callback($hook);
        }
    }
}
