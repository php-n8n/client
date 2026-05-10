<?php

declare(strict_types=1);

namespace PhpN8n\Client\Hooks;

use Throwable;

final readonly class HookContext
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        private LifecycleHook $hook,
        private array $context = [],
        private ?Throwable $exception = null,
    ) {
    }

    public function hook(): LifecycleHook
    {
        return $this->hook;
    }

    /**
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return $this->context;
    }

    public function exception(): ?Throwable
    {
        return $this->exception;
    }
}
