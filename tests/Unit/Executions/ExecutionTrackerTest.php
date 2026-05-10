<?php

declare(strict_types=1);

namespace PhpN8n\Client\Tests\Unit\Executions;

use PhpN8n\Client\Config\ApiConfig;
use PhpN8n\Client\Config\ExecutionFetchOptions;
use PhpN8n\Client\Config\PollingConfig;
use PhpN8n\Client\Exceptions\AuthenticationException;
use PhpN8n\Client\Exceptions\ExecutionTimeoutException;
use PhpN8n\Client\Executions\ExecutionReference;
use PhpN8n\Client\Executions\ExecutionStatus;
use PhpN8n\Client\Executions\ExecutionTracker;
use PhpN8n\Client\Hooks\LifecycleHook;
use PhpN8n\Client\Hooks\NullHookRunner;
use PhpN8n\Client\Http\RequestFactory;
use PhpN8n\Client\Http\ResponseDecoder;
use PhpN8n\Client\Tests\Fixtures\FakeHttpClient;
use PhpN8n\Client\Tests\Fixtures\HookRecorder;
use PhpN8n\Client\Tests\Fixtures\SimpleRequestFactory;
use PhpN8n\Client\Tests\Fixtures\SimpleResponse;
use PhpN8n\Client\Tests\Fixtures\SimpleStreamFactory;
use PhpN8n\Client\Tests\Fixtures\SimpleUri;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ExecutionTrackerTest extends TestCase
{
    #[Test]
    public function it_fetches_execution_details_using_an_execution_reference(): void
    {
        $httpClient = (new FakeHttpClient())->queueResponse(new SimpleResponse(
            body: '{"id":123,"status":"success","finished":true,"mode":"webhook"}',
            headers: ['Content-Type' => 'application/json'],
        ));
        $tracker = $this->tracker($httpClient);

        $result = $tracker->get(ExecutionReference::fromId(123), ExecutionFetchOptions::withData(false));

        $request = $httpClient->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame(
            'https://n8n.test/api/v1/executions/123?includeData=true&redactExecutionData=false',
            (string) $request->getUri(),
        );
        self::assertSame('api-key', $request->getHeaderLine('X-N8N-API-KEY'));
        self::assertSame('application/json', $request->getHeaderLine('Accept'));
        self::assertSame(ExecutionStatus::Success, $result->status());
    }

    #[Test]
    public function it_waits_until_an_execution_reaches_a_terminal_status(): void
    {
        $httpClient = (new FakeHttpClient())
            ->queueResponse(new SimpleResponse(
                body: '{"id":123,"status":"running","finished":false}',
                headers: ['Content-Type' => 'application/json'],
            ))
            ->queueResponse(new SimpleResponse(
                body: '{"id":123,"status":"success","finished":true}',
                headers: ['Content-Type' => 'application/json'],
            ));
        $hooks = new HookRecorder();
        $tracker = $this->tracker($httpClient, $hooks);

        $result = $tracker->wait(
            ExecutionReference::fromId(123),
            new PollingConfig(timeoutSeconds: 1, intervalMilliseconds: 0),
        );

        self::assertSame(ExecutionStatus::Success, $result->status());
        self::assertCount(2, $httpClient->requests());
        self::assertContains(LifecycleHook::BeforePollingStarts, $hooks->hooks());
        self::assertContains(LifecycleHook::ExecutionSucceeded, $hooks->hooks());
    }

    #[Test]
    public function it_times_out_when_an_execution_does_not_finish(): void
    {
        $httpClient = (new FakeHttpClient())->queueResponse(new SimpleResponse(
            body: '{"id":123,"status":"running","finished":false}',
            headers: ['Content-Type' => 'application/json'],
        ));
        $tracker = $this->tracker($httpClient);

        $this->expectException(ExecutionTimeoutException::class);

        $tracker->wait(
            ExecutionReference::fromId(123),
            new PollingConfig(timeoutSeconds: 0, intervalMilliseconds: 0),
        );
    }

    #[Test]
    public function it_throws_authentication_exceptions_for_unauthorized_api_responses(): void
    {
        $httpClient = (new FakeHttpClient())->queueResponse(new SimpleResponse(statusCode: 401));
        $tracker = $this->tracker($httpClient);

        $this->expectException(AuthenticationException::class);

        $tracker->get(ExecutionReference::fromId(123));
    }

    private function tracker(FakeHttpClient $httpClient, ?HookRecorder $hooks = null): ExecutionTracker
    {
        return new ExecutionTracker(
            $httpClient,
            new RequestFactory(new SimpleRequestFactory(), new SimpleStreamFactory()),
            new ResponseDecoder(),
            new ApiConfig(new SimpleUri('https://n8n.test/api/v1'), 'api-key'),
            hooks: $hooks ?? new NullHookRunner(),
        );
    }
}
