<?php

declare(strict_types=1);

namespace PhpN8n\Client\Webhooks;

use PhpN8n\Client\Contracts\HookRunnerContract;
use PhpN8n\Client\Contracts\WebhookResponseResolverContract;
use PhpN8n\Client\Contracts\WebhookTriggererContract;
use PhpN8n\Client\Exceptions\AuthenticationException;
use PhpN8n\Client\Exceptions\RequestException;
use PhpN8n\Client\Hooks\HookContext;
use PhpN8n\Client\Hooks\LifecycleHook;
use PhpN8n\Client\Hooks\NullHookRunner;
use PhpN8n\Client\Http\RequestFactory;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

final readonly class WebhookTriggerer implements WebhookTriggererContract
{
    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactory $requestFactory,
        private WebhookResponseResolverContract $resolver = new WebhookResponseResolver(),
        private HookRunnerContract $hooks = new NullHookRunner(),
    ) {
    }

    public function trigger(Webhook $webhook, ?WebhookRequest $request = null): WebhookResponse
    {
        $request ??= WebhookRequest::empty();
        $headers = array_replace($webhook->headers(), $request->headers());
        $query = array_replace($webhook->query(), $request->query());
        $httpRequest = match ($request->bodyType()) {
            WebhookRequestBodyType::Empty => $this->requestFactory->createEmpty(
                method: $webhook->method()->value,
                uri: $webhook->url(),
                headers: $headers,
                query: $query,
            ),
            WebhookRequestBodyType::Json => $this->requestFactory->createJson(
                method: $webhook->method()->value,
                uri: $webhook->url(),
                body: $request->body(),
                headers: $headers,
                query: $query,
            ),
            WebhookRequestBodyType::Text => $this->requestFactory->createText(
                method: $webhook->method()->value,
                uri: $webhook->url(),
                body: $request->textBody(),
                headers: $headers,
                query: $query,
            ),
            WebhookRequestBodyType::Stream => $this->requestFactory->createStream(
                method: $webhook->method()->value,
                uri: $webhook->url(),
                body: $request->streamBody(),
                headers: $headers,
                query: $query,
            ),
        };

        $this->hooks->run(new HookContext(LifecycleHook::BeforeWebhookRequest, [
            'webhook' => $webhook,
            'webhook_request' => $request,
            'http_request' => $httpRequest,
        ]));

        try {
            $httpResponse = $this->httpClient->sendRequest($httpRequest);
            $this->assertSuccessful($httpResponse);

            $this->hooks->run(new HookContext(LifecycleHook::AfterWebhookResponse, [
                'webhook' => $webhook,
                'webhook_request' => $request,
                'http_response' => $httpResponse,
            ]));

            $webhookResponse = $this->resolver->resolve($httpResponse);

            if ($webhookResponse->executionReference() !== null) {
                $this->hooks->run(new HookContext(LifecycleHook::ExecutionReferenceCreated, [
                    'reference' => $webhookResponse->executionReference(),
                    'webhook_response' => $webhookResponse,
                ]));
            }

            return $webhookResponse;
        } catch (ClientExceptionInterface $exception) {
            $wrapped = new RequestException('Unable to send webhook request to n8n.', previous: $exception);
            $this->hooks->run(new HookContext(LifecycleHook::ExceptionThrown, ['webhook' => $webhook], $wrapped));

            throw $wrapped;
        } catch (Throwable $exception) {
            $this->hooks->run(new HookContext(LifecycleHook::ExceptionThrown, ['webhook' => $webhook], $exception));

            throw $exception;
        }
    }

    private function assertSuccessful(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();

        if ($statusCode === 401) {
            throw new AuthenticationException(response: $response);
        }

        if ($statusCode >= 400) {
            throw new RequestException("Webhook request failed with status {$statusCode}.", $response);
        }
    }
}
