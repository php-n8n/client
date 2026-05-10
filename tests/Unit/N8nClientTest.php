<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Unit;

use PhpN8n\Client\Config\ApiConfig;
use PhpN8n\Client\Exceptions\N8nException;
use PhpN8n\Client\Executions\ExecutionReference;
use PhpN8n\Client\N8nClient;
use PhpN8n\Client\Tests\Fixtures\FakeHttpClient;
use PhpN8n\Client\Tests\Fixtures\SimpleRequestFactory;
use PhpN8n\Client\Tests\Fixtures\SimpleResponse;
use PhpN8n\Client\Tests\Fixtures\SimpleStreamFactory;
use PhpN8n\Client\Tests\Fixtures\SimpleUri;
use PhpN8n\Client\Webhooks\Webhook;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class N8nClientTest extends TestCase
{
    #[Test]
    public function it_can_trigger_webhooks_without_api_tracking_config(): void
    {
        $httpClient = (new FakeHttpClient())->queueResponse(new SimpleResponse(
            body: '{"ok":true}',
            headers: ['Content-Type' => 'application/json'],
        ));
        $client = new N8nClient($httpClient, new SimpleRequestFactory(), new SimpleStreamFactory());

        $response = $client->webhooks()->trigger(Webhook::fromUri(new SimpleUri('https://n8n.test/webhook/orders')));

        self::assertSame(['ok' => true], $response->body());
        self::assertNull($response->executionReference());
    }

    #[Test]
    public function it_requires_api_config_before_exposing_execution_tracking(): void
    {
        $client = new N8nClient(new FakeHttpClient(), new SimpleRequestFactory(), new SimpleStreamFactory());

        $this->expectException(N8nException::class);

        $client->executions();
    }

    #[Test]
    public function it_creates_execution_wrappers_from_references(): void
    {
        $client = new N8nClient(
            new FakeHttpClient(),
            new SimpleRequestFactory(),
            new SimpleStreamFactory(),
            new ApiConfig(new SimpleUri('https://n8n.test/api/v1'), 'api-key'),
        );

        $execution = $client->execution(ExecutionReference::fromId('123'));

        self::assertSame('123', $execution->reference()->id());
    }
}
