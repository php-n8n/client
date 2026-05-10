# PHP n8n Client

[![Latest Version](https://img.shields.io/packagist/v/php-n8n/client.svg)](https://packagist.org/packages/php-n8n/client)
[![PHP Version](https://img.shields.io/packagist/php-v/php-n8n/client.svg)](https://packagist.org/packages/php-n8n/client)
[![License](https://img.shields.io/packagist/l/php-n8n/client.svg)](LICENSE)
[![Tests](https://github.com/php-n8n/client/actions/workflows/ci.yml/badge.svg)](https://github.com/php-n8n/client/actions/workflows/ci.yml)
[![Coverage](https://img.shields.io/codecov/c/github/php-n8n/client.svg)](https://codecov.io/gh/php-n8n/client)
![PHPStan](https://img.shields.io/badge/PHPStan-level%206-blue.svg)
![Code Style](https://img.shields.io/badge/code%20style-PSR--12-blue.svg)
[![Downloads](https://img.shields.io/packagist/dt/php-n8n/client.svg)](https://packagist.org/packages/php-n8n/client)

A PSR-compliant PHP client for triggering and tracking n8n webhook executions. `php-n8n/client` is framework agnostic, immutable by design, and built on PSR-7, PSR-17, and PSR-18.

Full documentation:
https://php-n8n.com

The package focuses on webhook execution, execution tracking, and transport abstractions. It intentionally does not attempt to become a full n8n SDK.

## Installation

```bash
composer require php-n8n/client guzzlehttp/guzzle nyholm/psr7
```

This package depends on PSR interfaces. Install the PSR-18 HTTP client and PSR-17 factories that match your application stack.

## Minimal Example

```php
<?php

declare(strict_types=1);

use GuzzleHttp\Client as GuzzleClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use PhpN8n\Client\N8nClient;
use PhpN8n\Client\Webhooks\Webhook;
use PhpN8n\Client\Webhooks\WebhookRequest;

$psr17Factory = new Psr17Factory();

$client = new N8nClient(
    httpClient: new GuzzleClient(),
    requestFactory: $psr17Factory,
    streamFactory: $psr17Factory,
);

$response = $client->webhooks()->trigger(
    Webhook::fromUri($psr17Factory->createUri('https://n8n.example.com/webhook/order-created')),
    WebhookRequest::json([
        'orderId' => 'ORD-1001',
        'total' => 129.50,
    ]),
);

$body = $response->body();
```

Webhook URLs must be valid absolute `http` or `https` URLs with a host.

## Execution Tracking

Execution tracking uses the n8n API and requires an API key.

```php
use PhpN8n\Client\Config\ApiConfig;
use PhpN8n\Client\Config\ExecutionFetchOptions;
use PhpN8n\Client\Config\PollingConfig;
use PhpN8n\Client\Executions\ExecutionReference;

$client = new N8nClient(
    httpClient: new GuzzleClient(),
    requestFactory: $psr17Factory,
    streamFactory: $psr17Factory,
    apiConfig: new ApiConfig(
        apiUri: $psr17Factory->createUri('https://n8n.example.com/api/v1'),
        apiKey: $_ENV['N8N_API_KEY'],
    ),
);

$reference = ExecutionReference::fromId('123');

$result = $client->executions()->wait(
    $reference,
    new PollingConfig(
        timeoutSeconds: 60,
        intervalMilliseconds: 1000,
        fetchOptions: ExecutionFetchOptions::withData(),
    ),
);
```

Webhook responses can also expose an execution reference when n8n returns one:

```php
$reference = $response->executionReference();

if ($reference !== null) {
    $result = $client->execution($reference)->wait();
}
```

## Why This Package Exists

This package exists to provide a lightweight, composable, and standards-oriented way to interact with n8n webhooks from PHP applications.

The ecosystem already contains several wrappers and framework-specific integrations, but many of them are tightly coupled to specific frameworks, HTTP clients, or large abstractions.

`php-n8n/client` focuses on PSR compliance, framework agnostic architecture, immutable APIs, minimal dependencies, explicit behavior, composability, and predictable integrations.

The goal is to provide a stable foundation that can be integrated into any PHP application or framework.

## Design Principles

- PSR-first architecture
- Framework agnostic design
- Immutable value objects
- Explicit APIs over magic behavior
- Composable integrations
- Narrow responsibilities
- Stable public APIs
- Semantic versioning
- Minimal dependencies

The core package intentionally remains small and focused.

## Ecosystem

The `php-n8n` ecosystem is designed as a collection of composable packages.

The core client remains intentionally minimal and framework agnostic. Additional integrations and higher-level tooling are distributed as separate packages.

Planned ecosystem direction:

```text
php-n8n/client
php-n8n/contracts
php-n8n/laravel
php-n8n/symfony
php-n8n/testing
php-n8n/events
php-n8n/execution-tracker
```

This structure allows the ecosystem to grow without introducing unnecessary complexity into the core package.

## Contributing

Contributions, ideas, bug reports, and improvements are welcome.

If you discover a bug, have a feature suggestion, or want to improve the project, please open an issue before submitting large changes. This helps discuss the direction and avoid duplicated work.

When contributing:

- Follow the existing coding standards
- Include tests when applicable
- Keep changes focused and minimal
- Maintain PSR compliance and framework agnostic design

For bug reports, please include the PHP version, package version, reproduction steps, expected behavior, and actual behavior.

For feature requests, please explain the problem being solved, the proposed API or design, and potential tradeoffs or alternatives.

Run the project checks before submitting changes:

```bash
composer check
```

## Versioning Policy

This package follows Semantic Versioning.

The first public tag is expected to be `v1.0.0`, marking the client API as stable from the initial release.

Breaking changes will only be introduced in future major releases.

## License

The MIT License. See [LICENSE](LICENSE).
