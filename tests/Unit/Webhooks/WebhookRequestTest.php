<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Unit\Webhooks;

use PhpN8n\Client\Tests\Fixtures\SimpleStream;
use PhpN8n\Client\Webhooks\WebhookRequest;
use PhpN8n\Client\Webhooks\WebhookRequestBodyType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class WebhookRequestTest extends TestCase
{
    #[Test]
    public function it_creates_explicit_body_types(): void
    {
        $stream = new SimpleStream('payload');

        self::assertSame(WebhookRequestBodyType::Empty, WebhookRequest::empty()->bodyType());
        self::assertSame(WebhookRequestBodyType::Json, WebhookRequest::json(['ok' => true])->bodyType());
        self::assertSame(WebhookRequestBodyType::Text, WebhookRequest::text('payload')->bodyType());
        self::assertSame('payload', WebhookRequest::text('payload')->textBody());
        self::assertSame(WebhookRequestBodyType::Stream, WebhookRequest::stream($stream)->bodyType());
        self::assertSame($stream, WebhookRequest::stream($stream)->streamBody());
    }

    #[Test]
    public function it_preserves_metadata_when_replacing_body(): void
    {
        $request = WebhookRequest::empty()
            ->withHeader('X-Request-ID', 'req-1')
            ->withQueryParameter('dryRun', true)
            ->withJsonBody(['orderId' => 123]);

        self::assertSame(WebhookRequestBodyType::Json, $request->bodyType());
        self::assertSame(['X-Request-ID' => 'req-1'], $request->headers());
        self::assertSame(['dryRun' => true], $request->query());
        self::assertSame(['orderId' => 123], $request->body());
    }
}
