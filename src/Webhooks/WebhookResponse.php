<?php

declare(strict_types=1);

namespace PhpN8n\Client\Webhooks;

use PhpN8n\Client\Executions\ExecutionReference;
use PhpN8n\Client\Executions\ExecutionStatus;
use Psr\Http\Message\ResponseInterface;

final readonly class WebhookResponse
{
    public function __construct(
        private ResponseInterface $response,
        private WebhookResponseType $type,
        private mixed $body = null,
        private ?ExecutionReference $executionReference = null,
        private ?ExecutionStatus $status = null,
    ) {
    }

    public function httpResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function statusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function type(): WebhookResponseType
    {
        return $this->type;
    }

    public function body(): mixed
    {
        return $this->body;
    }

    public function executionReference(): ?ExecutionReference
    {
        return $this->executionReference;
    }

    public function status(): ?ExecutionStatus
    {
        return $this->status;
    }
}
