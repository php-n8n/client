<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Unit\Executions;

use InvalidArgumentException;
use PhpN8n\Client\Executions\ExecutionReference;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ExecutionReferenceTest extends TestCase
{
    #[Test]
    public function it_can_be_constructed_from_an_execution_id(): void
    {
        $reference = ExecutionReference::fromId(' 123 ');

        self::assertSame('123', $reference->id());
        self::assertSame('123', (string) $reference);
    }

    #[Test]
    public function it_rejects_empty_execution_ids(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ExecutionReference::fromId('');
    }
}
