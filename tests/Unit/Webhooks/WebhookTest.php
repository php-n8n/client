<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Unit\Webhooks;

use InvalidArgumentException;
use PhpN8n\Client\Tests\Fixtures\SimpleUri;
use PhpN8n\Client\Webhooks\Webhook;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class WebhookTest extends TestCase
{
    #[Test]
    public function it_accepts_absolute_http_urls(): void
    {
        $webhook = Webhook::fromUri(new SimpleUri('https://n8n.test/webhook/orders'));

        self::assertSame('https://n8n.test/webhook/orders', (string) $webhook->url());
    }

    #[Test]
    public function it_rejects_urls_without_a_scheme(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Webhook::fromUri(new SimpleUri('//n8n.test/webhook/orders'));
    }

    #[Test]
    public function it_rejects_relative_urls(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Webhook::fromUri(new SimpleUri('/webhook/orders'));
    }

    #[Test]
    public function it_rejects_urls_without_a_host(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Webhook::fromUri(new SimpleUri('https:///webhook/orders'));
    }

    #[Test]
    public function it_rejects_malformed_urls(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Webhook::fromUri(new SimpleUri('https://n8n.test/webhook/orders with spaces'));
    }
}
