<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Unit\Executions;

use PhpN8n\Client\Executions\ExecutionResult;
use PhpN8n\Client\Executions\ExecutionStatus;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ExecutionResultTest extends TestCase
{
    #[Test]
    public function it_maps_n8n_execution_payloads_into_a_typed_result(): void
    {
        $result = ExecutionResult::fromN8nPayload([
            'id' => 1000,
            'status' => 'success',
            'finished' => true,
            'mode' => 'webhook',
            'workflowId' => 'workflow-1',
            'startedAt' => '2026-05-08T10:00:00.000Z',
            'stoppedAt' => '2026-05-08T10:00:02.000Z',
            'data' => ['resultData' => ['runData' => []]],
            'customData' => ['customer' => 'acme'],
        ]);

        self::assertSame('1000', $result->reference()->id());
        self::assertSame(ExecutionStatus::Success, $result->status());
        self::assertSame('success', $result->rawStatus());
        self::assertTrue($result->finished());
        self::assertSame('webhook', $result->mode());
        self::assertSame('workflow-1', $result->workflowId());
        self::assertSame(['resultData' => ['runData' => []]], $result->data()?->payload());
        self::assertSame(['customer' => 'acme'], $result->data()?->customData());
    }
}
