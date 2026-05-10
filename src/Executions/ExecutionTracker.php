<?php

declare(strict_types=1);

namespace PhpN8n\Client\Executions;

use JsonException;
use PhpN8n\Client\Config\ApiConfig;
use PhpN8n\Client\Config\ExecutionFetchOptions;
use PhpN8n\Client\Config\PollingConfig;
use PhpN8n\Client\Contracts\ExecutionTrackerContract;
use PhpN8n\Client\Contracts\HookRunnerContract;
use PhpN8n\Client\Exceptions\AuthenticationException;
use PhpN8n\Client\Exceptions\ExecutionTimeoutException;
use PhpN8n\Client\Exceptions\RequestException;
use PhpN8n\Client\Hooks\HookContext;
use PhpN8n\Client\Hooks\LifecycleHook;
use PhpN8n\Client\Hooks\NullHookRunner;
use PhpN8n\Client\Http\ApiRequestSigner;
use PhpN8n\Client\Http\RequestFactory;
use PhpN8n\Client\Http\ResponseDecoder;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Throwable;

final readonly class ExecutionTracker implements ExecutionTrackerContract
{
    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactory $requestFactory,
        private ResponseDecoder $responseDecoder,
        private ApiConfig $apiConfig,
        private ApiRequestSigner $signer = new ApiRequestSigner(),
        private HookRunnerContract $hooks = new NullHookRunner(),
    ) {
    }

    public function get(ExecutionReference $reference, ?ExecutionFetchOptions $options = null): ExecutionResult
    {
        $options ??= ExecutionFetchOptions::default();
        $this->hooks->run(new HookContext(LifecycleHook::BeforeExecutionRefresh, [
            'reference' => $reference,
            'options' => $options,
        ]));

        $request = $this->requestFactory->createEmpty(
            method: 'GET',
            uri: $this->executionUri($reference),
            query: $options->toQuery(),
        );
        $request = $this->signer->sign($request, $this->apiConfig);

        try {
            $response = $this->httpClient->sendRequest($request);
            $result = $this->mapResponse($response);
        } catch (ClientExceptionInterface $exception) {
            $wrapped = new RequestException('Unable to send n8n API request.', previous: $exception);
            $this->hooks->run(new HookContext(LifecycleHook::ExceptionThrown, [
                'reference' => $reference,
            ], $wrapped));

            throw $wrapped;
        } catch (Throwable $exception) {
            $this->hooks->run(new HookContext(LifecycleHook::ExceptionThrown, [
                'reference' => $reference,
            ], $exception));

            throw $exception;
        }

        $this->hooks->run(new HookContext(LifecycleHook::AfterExecutionRefresh, [
            'reference' => $reference,
            'result' => $result,
        ]));

        return $result;
    }

    public function wait(ExecutionReference $reference, ?PollingConfig $polling = null): ExecutionResult
    {
        $polling ??= PollingConfig::default();
        $startedAt = microtime(true);
        $attempt = 0;
        $previousStatus = null;

        $this->hooks->run(new HookContext(LifecycleHook::BeforePollingStarts, [
            'reference' => $reference,
            'polling' => $polling,
        ]));

        while (true) {
            ++$attempt;

            $this->hooks->run(new HookContext(LifecycleHook::PollingAttempt, [
                'reference' => $reference,
                'attempt' => $attempt,
            ]));

            $result = $this->get($reference, $polling->fetchOptions());

            if ($previousStatus !== null && $previousStatus !== $result->status()) {
                $this->hooks->run(new HookContext(LifecycleHook::ExecutionStatusChanged, [
                    'reference' => $reference,
                    'previous_status' => $previousStatus,
                    'status' => $result->status(),
                    'result' => $result,
                ]));
            }

            $previousStatus = $result->status();

            if ($result->status()->isTerminal()) {
                $hook = $result->status()->isSuccessful()
                    ? LifecycleHook::ExecutionSucceeded
                    : LifecycleHook::ExecutionFailed;

                $this->hooks->run(new HookContext($hook, ['reference' => $reference, 'result' => $result]));

                return $result;
            }

            if ((microtime(true) - $startedAt) >= $polling->timeoutSeconds()) {
                $exception = new ExecutionTimeoutException($reference, $polling->timeoutSeconds());
                $this->hooks->run(new HookContext(LifecycleHook::ExecutionTimedOut, [
                    'reference' => $reference,
                    'last_result' => $result,
                ], $exception));

                throw $exception;
            }

            if ($polling->intervalMilliseconds() > 0) {
                usleep($polling->intervalMilliseconds() * 1000);
            }
        }
    }

    private function mapResponse(ResponseInterface $response): ExecutionResult
    {
        $statusCode = $response->getStatusCode();

        if ($statusCode === 401) {
            throw new AuthenticationException(response: $response);
        }

        if ($statusCode >= 400) {
            throw new RequestException("n8n API request failed with status {$statusCode}.", $response);
        }

        try {
            $payload = $this->responseDecoder->json($response);
        } catch (JsonException $exception) {
            throw new RequestException('n8n execution response was not valid JSON.', $response, $exception);
        }

        if (! is_array($payload)) {
            throw new RequestException('n8n execution response did not contain a JSON object.', $response);
        }

        return ExecutionResult::fromN8nPayload($payload);
    }

    private function executionUri(ExecutionReference $reference): UriInterface
    {
        $apiUri = $this->apiConfig->uri();

        return $apiUri->withPath(rtrim($apiUri->getPath(), '/') . '/executions/' . rawurlencode($reference->id()));
    }
}
