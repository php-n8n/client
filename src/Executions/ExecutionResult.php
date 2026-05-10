<?php

declare(strict_types=1);

namespace PhpN8n\Client\Executions;

use DateTimeImmutable;
use Exception;
use PhpN8n\Client\Exceptions\RequestException;

final readonly class ExecutionResult
{
    public function __construct(
        private ExecutionReference $reference,
        private ExecutionStatus $status,
        private ?string $rawStatus = null,
        private bool $finished = false,
        private ?string $mode = null,
        private ?string $workflowId = null,
        private ?DateTimeImmutable $startedAt = null,
        private ?DateTimeImmutable $stoppedAt = null,
        private ?DateTimeImmutable $waitTill = null,
        private ?ExecutionData $data = null,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromN8nPayload(array $payload): self
    {
        $id = $payload['id'] ?? null;

        if (! is_int($id) && ! is_string($id)) {
            throw new RequestException('n8n execution response did not contain a valid execution ID.');
        }

        $rawStatus = is_string($payload['status'] ?? null) ? $payload['status'] : null;
        $status = ExecutionStatus::fromN8nStatus($rawStatus);
        $data = array_key_exists('data', $payload) || array_key_exists('customData', $payload)
            ? new ExecutionData(
                $payload['data'] ?? null,
                is_array($payload['customData'] ?? null) ? $payload['customData'] : [],
            )
            : null;

        return new self(
            reference: ExecutionReference::fromId($id),
            status: $status,
            rawStatus: $rawStatus,
            finished: is_bool($payload['finished'] ?? null) ? $payload['finished'] : $status->isTerminal(),
            mode: is_string($payload['mode'] ?? null) ? $payload['mode'] : null,
            workflowId: isset($payload['workflowId']) ? (string) $payload['workflowId'] : null,
            startedAt: self::date($payload['startedAt'] ?? null),
            stoppedAt: self::date($payload['stoppedAt'] ?? null),
            waitTill: self::date($payload['waitTill'] ?? null),
            data: $data,
        );
    }

    public function reference(): ExecutionReference
    {
        return $this->reference;
    }

    public function status(): ExecutionStatus
    {
        return $this->status;
    }

    public function rawStatus(): ?string
    {
        return $this->rawStatus;
    }

    public function finished(): bool
    {
        return $this->finished;
    }

    public function mode(): ?string
    {
        return $this->mode;
    }

    public function workflowId(): ?string
    {
        return $this->workflowId;
    }

    public function startedAt(): ?DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function stoppedAt(): ?DateTimeImmutable
    {
        return $this->stoppedAt;
    }

    public function waitTill(): ?DateTimeImmutable
    {
        return $this->waitTill;
    }

    public function data(): ?ExecutionData
    {
        return $this->data;
    }

    private static function date(mixed $value): ?DateTimeImmutable
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Exception) {
            return null;
        }
    }
}
