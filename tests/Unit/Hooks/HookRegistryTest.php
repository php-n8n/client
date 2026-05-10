<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Unit\Hooks;

use PhpN8n\Client\Hooks\HookContext;
use PhpN8n\Client\Hooks\HookRegistry;
use PhpN8n\Client\Hooks\LifecycleHook;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class HookRegistryTest extends TestCase
{
    #[Test]
    public function it_runs_registered_closures_for_the_matching_hook(): void
    {
        $calls = [];

        $registry = (new HookRegistry())
            ->on(LifecycleHook::BeforeWebhookRequest, function (HookContext $hook) use (&$calls): void {
                $calls[] = $hook->hook();
                $calls[] = $hook->context()['request_id'];
            })
            ->on(LifecycleHook::AfterWebhookResponse, function (HookContext $_hook) use (&$calls): void {
                $calls[] = 'ignored';
            });

        $registry->run(new HookContext(LifecycleHook::BeforeWebhookRequest, ['request_id' => 'req-1']));

        self::assertSame([LifecycleHook::BeforeWebhookRequest, 'req-1'], $calls);
    }

    #[Test]
    public function it_runs_multiple_closures_in_registration_order(): void
    {
        $calls = [];

        $registry = (new HookRegistry())
            ->on(LifecycleHook::PollingAttempt, function (HookContext $_hook) use (&$calls): void {
                $calls[] = 'first';
            })
            ->on(LifecycleHook::PollingAttempt, function (HookContext $_hook) use (&$calls): void {
                $calls[] = 'second';
            });

        $registry->run(new HookContext(LifecycleHook::PollingAttempt));

        self::assertSame(['first', 'second'], $calls);
    }
}
