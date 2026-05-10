<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Unit\Executions;

use PhpN8n\Client\Executions\ExecutionStatus;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ExecutionStatusTest extends TestCase
{
    #[Test]
    public function it_maps_n8n_failure_statuses_to_failed(): void
    {
        self::assertSame(ExecutionStatus::Failed, ExecutionStatus::fromN8nStatus('error'));
        self::assertSame(ExecutionStatus::Failed, ExecutionStatus::fromN8nStatus('crashed'));
        self::assertSame(ExecutionStatus::Failed, ExecutionStatus::fromN8nStatus('failed'));
    }

    #[Test]
    public function it_identifies_terminal_statuses(): void
    {
        self::assertSame(true, ExecutionStatus::Success->isTerminal());
        self::assertSame(true, ExecutionStatus::Failed->isTerminal());
        self::assertSame(true, ExecutionStatus::Canceled->isTerminal());
        self::assertSame(false, ExecutionStatus::Running->isTerminal());
    }
}
