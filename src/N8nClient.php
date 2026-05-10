<?php

declare(strict_types=1);

namespace PhpN8n\Client;

use PhpN8n\Client\Config\ApiConfig;
use PhpN8n\Client\Contracts\ExecutionTrackerContract;
use PhpN8n\Client\Contracts\HookRunnerContract;
use PhpN8n\Client\Contracts\WebhookResponseResolverContract;
use PhpN8n\Client\Contracts\WebhookTriggererContract;
use PhpN8n\Client\Exceptions\N8nException;
use PhpN8n\Client\Executions\ExecutionReference;
use PhpN8n\Client\Executions\ExecutionTracker;
use PhpN8n\Client\Hooks\NullHookRunner;
use PhpN8n\Client\Http\RequestFactory;
use PhpN8n\Client\Http\ResponseDecoder;
use PhpN8n\Client\Webhooks\WebhookResponseResolver;
use PhpN8n\Client\Webhooks\WebhookTriggerer;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final readonly class N8nClient
{
    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        private ?ApiConfig $apiConfig = null,
        private ?WebhookResponseResolverContract $webhookResponseResolver = null,
        private HookRunnerContract $hooks = new NullHookRunner(),
        private ?WebhookTriggererContract $webhookTriggerer = null,
        private ?ExecutionTrackerContract $executionTracker = null,
    ) {
    }

    public function webhooks(): WebhookTriggererContract
    {
        return $this->webhookTriggerer ?? new WebhookTriggerer(
            $this->httpClient,
            $this->httpRequestFactory(),
            $this->webhookResponseResolver ?? new WebhookResponseResolver($this->responseDecoder()),
            $this->hooks,
        );
    }

    public function executions(): ExecutionTrackerContract
    {
        if ($this->executionTracker !== null) {
            return $this->executionTracker;
        }

        if ($this->apiConfig === null) {
            throw new N8nException('Execution tracking requires an API config.');
        }

        return new ExecutionTracker(
            $this->httpClient,
            $this->httpRequestFactory(),
            $this->responseDecoder(),
            $this->apiConfig,
            hooks: $this->hooks,
        );
    }

    public function execution(ExecutionReference $reference): Execution
    {
        return new Execution($reference, $this->executions());
    }

    private function httpRequestFactory(): RequestFactory
    {
        return new RequestFactory($this->requestFactory, $this->streamFactory);
    }

    private function responseDecoder(): ResponseDecoder
    {
        return new ResponseDecoder();
    }
}
