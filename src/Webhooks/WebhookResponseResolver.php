<?php

declare(strict_types=1);

namespace PhpN8n\Client\Webhooks;

use JsonException;
use PhpN8n\Client\Contracts\WebhookResponseResolverContract;
use PhpN8n\Client\Exceptions\InvalidWebhookResponseException;
use PhpN8n\Client\Executions\ExecutionReference;
use PhpN8n\Client\Executions\ExecutionStatus;
use PhpN8n\Client\Http\ResponseDecoder;
use Psr\Http\Message\ResponseInterface;

final readonly class WebhookResponseResolver implements WebhookResponseResolverContract
{
    public function __construct(private ResponseDecoder $decoder = new ResponseDecoder())
    {
    }

    public function resolve(ResponseInterface $response): WebhookResponse
    {
        [$body, $type] = $this->resolveBody($response);

        return new WebhookResponse(
            response: $response,
            type: $type,
            body: $body,
            executionReference: $this->executionReference($body),
            status: $this->status($body),
        );
    }

    /**
     * @return array{0: mixed, 1: WebhookResponseType}
     */
    private function resolveBody(ResponseInterface $response): array
    {
        $body = $this->decoder->body($response);

        if (trim($body) === '') {
            return [null, WebhookResponseType::None];
        }

        if (! $this->decoder->isJson($response)) {
            return [$body, WebhookResponseType::Text];
        }

        try {
            return [json_decode($body, true, 512, JSON_THROW_ON_ERROR), WebhookResponseType::Json];
        } catch (JsonException $exception) {
            throw new InvalidWebhookResponseException(
                'Webhook response body was not valid JSON.',
                previous: $exception,
            );
        }
    }

    private function executionReference(mixed $body): ?ExecutionReference
    {
        if (! is_array($body)) {
            return null;
        }

        $id = $body['executionId'] ?? $body['execution_id'] ?? null;

        if ($id === null && is_array($body['executionReference'] ?? null)) {
            $id = $body['executionReference']['id'] ?? null;
        }

        if ($id === null && is_array($body['execution'] ?? null)) {
            $id = $body['execution']['id'] ?? null;
        }

        if (! is_int($id) && ! is_string($id)) {
            return null;
        }

        return ExecutionReference::fromId($id);
    }

    private function status(mixed $body): ?ExecutionStatus
    {
        if (! is_array($body)) {
            return null;
        }

        $status = $body['status'] ?? null;

        if (! is_string($status) && is_array($body['execution'] ?? null)) {
            $status = $body['execution']['status'] ?? null;
        }

        return is_string($status) ? ExecutionStatus::fromN8nStatus($status) : null;
    }
}
