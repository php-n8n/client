<?php

declare(strict_types=1);

namespace PhpN8n\Client\Executions;

enum ExecutionStatus: string
{
    case New = 'new';
    case Running = 'running';
    case Success = 'success';
    case Failed = 'failed';
    case Canceled = 'canceled';
    case Waiting = 'waiting';
    case Unknown = 'unknown';

    public static function fromN8nStatus(?string $status): self
    {
        return match ($status) {
            'new' => self::New,
            'running' => self::Running,
            'success' => self::Success,
            'error', 'crashed', 'failed' => self::Failed,
            'canceled' => self::Canceled,
            'waiting' => self::Waiting,
            default => self::Unknown,
        };
    }

    public function isTerminal(): bool
    {
        return match ($this) {
            self::Success, self::Failed, self::Canceled => true,
            self::New, self::Running, self::Waiting, self::Unknown => false,
        };
    }

    public function isSuccessful(): bool
    {
        return $this === self::Success;
    }

    public function isFailure(): bool
    {
        return $this === self::Failed || $this === self::Canceled;
    }
}
