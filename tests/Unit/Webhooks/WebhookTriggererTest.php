<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Unit\Webhooks;

use PhpN8n\Client\Hooks\LifecycleHook;
use PhpN8n\Client\Http\RequestFactory;
use PhpN8n\Client\Tests\Fixtures\FakeHttpClient;
use PhpN8n\Client\Tests\Fixtures\HookRecorder;
use PhpN8n\Client\Tests\Fixtures\SimpleRequestFactory;
use PhpN8n\Client\Tests\Fixtures\SimpleResponse;
use PhpN8n\Client\Tests\Fixtures\SimpleStreamFactory;
use PhpN8n\Client\Tests\Fixtures\SimpleUri;
use PhpN8n\Client\Webhooks\Webhook;
use PhpN8n\Client\Webhooks\WebhookRequest;
use PhpN8n\Client\Webhooks\WebhookTriggerer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class WebhookTriggererTest extends TestCase
{
    #[Test]
    public function it_triggers_a_webhook_and_returns_a_typed_response_without_tracking(): void
    {
        $httpClient = (new FakeHttpClient())->queueResponse(new SimpleResponse(
            body: '{"executionId":"abc123","status":"new","data":{"queued":true}}',
            headers: ['Content-Type' => 'application/json'],
        ));
        $hooks = new HookRecorder();
        $triggerer = new WebhookTriggerer(
            $httpClient,
            new RequestFactory(new SimpleRequestFactory(), new SimpleStreamFactory()),
            hooks: $hooks,
        );

        $response = $triggerer->trigger(
            Webhook::fromUri(new SimpleUri('https://n8n.test/webhook/orders'))
                ->withMethod('post')
                ->withHeader('X-Webhook-Token', 'webhook-secret')
                ->withQueryParameter('source', 'php'),
            WebhookRequest::json(['orderId' => 123])->withHeader('X-Request-ID', 'req-1'),
        );

        $request = $httpClient->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('https://n8n.test/webhook/orders?source=php', (string) $request->getUri());
        self::assertSame('webhook-secret', $request->getHeaderLine('X-Webhook-Token'));
        self::assertSame('req-1', $request->getHeaderLine('X-Request-ID'));
        self::assertJsonStringEqualsJsonString('{"orderId":123}', (string) $request->getBody());
        self::assertSame('abc123', $response->executionReference()?->id());
        self::assertContains(LifecycleHook::BeforeWebhookRequest, $hooks->hooks());
        self::assertContains(LifecycleHook::AfterWebhookResponse, $hooks->hooks());
        self::assertContains(LifecycleHook::ExecutionReferenceCreated, $hooks->hooks());
    }

    #[Test]
    public function request_headers_and_query_override_webhook_defaults(): void
    {
        $httpClient = (new FakeHttpClient())->queueResponse(new SimpleResponse(
            body: '{"ok":true}',
            headers: ['Content-Type' => 'application/json'],
        ));
        $triggerer = new WebhookTriggerer(
            $httpClient,
            new RequestFactory(new SimpleRequestFactory(), new SimpleStreamFactory()),
        );

        $triggerer->trigger(
            Webhook::fromUri(new SimpleUri('https://n8n.test/webhook/orders?existing=1'))
                ->withHeader('X-Request-ID', 'default')
                ->withQueryParameter('tenant', 'default')
                ->withQueryParameter('source', 'webhook'),
            WebhookRequest::empty()
                ->withHeader('X-Request-ID', 'request')
                ->withQueryParameter('tenant', 'request'),
        );

        $request = $httpClient->lastRequest();

        self::assertSame('request', $request->getHeaderLine('X-Request-ID'));
        self::assertSame(
            'https://n8n.test/webhook/orders?existing=1&tenant=request&source=webhook',
            (string) $request->getUri(),
        );
    }
}
