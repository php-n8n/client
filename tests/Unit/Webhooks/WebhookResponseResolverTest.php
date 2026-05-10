<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Unit\Webhooks;

use PhpN8n\Client\Executions\ExecutionStatus;
use PhpN8n\Client\Tests\Fixtures\SimpleResponse;
use PhpN8n\Client\Webhooks\WebhookResponseType;
use PhpN8n\Client\Webhooks\WebhookResponseResolver;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class WebhookResponseResolverTest extends TestCase
{
    #[Test]
    public function it_extracts_optional_execution_reference_status_and_body_from_json(): void
    {
        $resolver = new WebhookResponseResolver();
        $response = new SimpleResponse(
            body: '{"executionId":123,"status":"running","data":{"accepted":true}}',
            headers: ['Content-Type' => 'application/json'],
        );

        $webhookResponse = $resolver->resolve($response);

        self::assertSame('123', $webhookResponse->executionReference()?->id());
        self::assertSame(ExecutionStatus::Running, $webhookResponse->status());
        self::assertSame([
            'executionId' => 123,
            'status' => 'running',
            'data' => ['accepted' => true],
        ], $webhookResponse->body());
        self::assertSame(WebhookResponseType::Json, $webhookResponse->type());
    }

    #[Test]
    public function it_keeps_the_raw_response_body_readable_after_resolving(): void
    {
        $resolver = new WebhookResponseResolver();
        $response = new SimpleResponse(
            body: '{"executionId":123}',
            headers: ['Content-Type' => 'application/json'],
        );

        $webhookResponse = $resolver->resolve($response);

        self::assertSame('{"executionId":123}', $webhookResponse->httpResponse()->getBody()->getContents());
    }

    #[Test]
    public function it_treats_empty_responses_as_no_response_body(): void
    {
        $resolver = new WebhookResponseResolver();

        $webhookResponse = $resolver->resolve(new SimpleResponse(body: ''));

        self::assertNull($webhookResponse->body());
        self::assertNull($webhookResponse->executionReference());
        self::assertSame(WebhookResponseType::None, $webhookResponse->type());
    }

    #[Test]
    public function it_does_not_assume_custom_json_contains_an_execution_reference(): void
    {
        $resolver = new WebhookResponseResolver();
        $response = new SimpleResponse(
            body: '{"customerId":55,"accepted":true}',
            headers: ['Content-Type' => 'application/json'],
        );

        $webhookResponse = $resolver->resolve($response);

        self::assertNull($webhookResponse->executionReference());
        self::assertSame(['customerId' => 55, 'accepted' => true], $webhookResponse->body());
        self::assertSame(WebhookResponseType::Json, $webhookResponse->type());
    }

    #[Test]
    public function it_treats_plain_text_responses_as_text(): void
    {
        $resolver = new WebhookResponseResolver();

        $webhookResponse = $resolver->resolve(new SimpleResponse(body: 'accepted'));

        self::assertSame('accepted', $webhookResponse->body());
        self::assertSame(WebhookResponseType::Text, $webhookResponse->type());
    }
}
