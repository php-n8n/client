# Contributing

Contributions, ideas, bug reports, and improvements are welcome.

Full documentation:
https://php-n8n.com

Before submitting large changes, please open an issue or discussion so the direction can be agreed on early.

## Project Direction

`php-n8n/client` is a lightweight, PSR-compliant PHP client for triggering and tracking n8n webhook executions.

The core package should remain:

- Framework agnostic
- PSR-first
- Immutable by design
- Explicit rather than magical
- Small and composable
- Focused on webhook execution, execution tracking, and transport abstractions

The package intentionally does not aim to become a full n8n SDK.

## Development

Install dependencies:

```bash
composer install
```

Run the full project checks:

```bash
composer check
```

This runs Composer validation, PHPUnit, PSR-12 checks, and PHPStan.

## Pull Requests

When contributing code:

- Follow the existing coding standards
- Include tests when applicable
- Keep changes focused and minimal
- Maintain PSR compliance
- Avoid framework-specific dependencies in the core client
- Update documentation when behavior or public APIs change

## Bug Reports

For bug reports, please include:

- PHP version
- Package version
- Relevant PSR-7, PSR-17, and PSR-18 implementations
- Reproduction steps
- Expected behavior
- Actual behavior

## Feature Requests

For feature requests, please explain:

- The problem being solved
- The proposed API or design
- Potential tradeoffs or alternatives
- Whether the feature belongs in the core package or a separate ecosystem package

Discussion and feedback are welcome through GitHub Issues and Discussions.
