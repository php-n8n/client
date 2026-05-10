<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Unit\Http;

use PhpN8n\Client\Http\RequestFactory;
use PhpN8n\Client\Tests\Fixtures\SimpleRequestFactory;
use PhpN8n\Client\Tests\Fixtures\SimpleStream;
use PhpN8n\Client\Tests\Fixtures\SimpleStreamFactory;
use PhpN8n\Client\Tests\Fixtures\SimpleUri;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RequestFactoryTest extends TestCase
{
    #[Test]
    public function it_creates_json_requests_with_query_parameters_and_headers(): void
    {
        $factory = new RequestFactory(new SimpleRequestFactory(), new SimpleStreamFactory());

        $request = $factory->createJson(
            method: 'post',
            uri: new SimpleUri('https://n8n.test/webhook/orders'),
            body: ['orderId' => 123],
            headers: ['X-Webhook-Token' => 'secret'],
            query: ['customer' => 'Ada Lovelace'],
        );

        self::assertSame('POST', $request->getMethod());
        self::assertSame('https://n8n.test/webhook/orders?customer=Ada%20Lovelace', (string) $request->getUri());
        self::assertSame('secret', $request->getHeaderLine('X-Webhook-Token'));
        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
        self::assertJsonStringEqualsJsonString('{"orderId":123}', (string) $request->getBody());
    }

    #[Test]
    public function it_appends_query_parameters_before_uri_fragments(): void
    {
        $factory = new RequestFactory(new SimpleRequestFactory(), new SimpleStreamFactory());

        $request = $factory->createEmpty(
            method: 'GET',
            uri: new SimpleUri('https://n8n.test/webhook/orders#result'),
            query: ['customer' => 'Ada Lovelace'],
        );

        self::assertSame(
            'https://n8n.test/webhook/orders?customer=Ada%20Lovelace#result',
            (string) $request->getUri(),
        );
    }

    #[Test]
    public function it_creates_text_requests(): void
    {
        $factory = new RequestFactory(new SimpleRequestFactory(), new SimpleStreamFactory());

        $request = $factory->createText(
            method: 'POST',
            uri: new SimpleUri('https://n8n.test/webhook/orders'),
            body: 'plain payload',
        );

        self::assertSame('text/plain', $request->getHeaderLine('Content-Type'));
        self::assertSame('plain payload', (string) $request->getBody());
    }

    #[Test]
    public function it_creates_stream_requests(): void
    {
        $factory = new RequestFactory(new SimpleRequestFactory(), new SimpleStreamFactory());
        $stream = new SimpleStream('stream payload');

        $request = $factory->createStream(
            method: 'POST',
            uri: new SimpleUri('https://n8n.test/webhook/orders'),
            body: $stream,
        );

        self::assertSame($stream, $request->getBody());
        self::assertSame('', $request->getHeaderLine('Content-Type'));
    }
}
