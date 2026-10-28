# Changelog

All notable changes to `php-n8n/client` will be documented in this file.

Full documentation:
https://php-n8n.com

This project follows [Semantic Versioning](https://semver.org/).

## v1.0.4 - 2026-10-28

- Added package `authors` metadata to `composer.json`.
- Allowed `squizlabs/php_codesniffer` ^4.0 as a development dependency.

## v1.0.3 - 2026-10-28

- Pinned all GitHub Actions to full commit SHAs.
- Upgraded `actions/checkout`, `actions/upload-artifact`, and `codecov/codecov-action` to v7.
- Fixed a template injection in the post-CI build workflow by passing the dispatch payload through `env` and validating the commit SHA.
- Restricted the default `GITHUB_TOKEN` permissions in CI to read-only and disabled checkout credential persistence.
- Added `composer audit` to CI.
- Added Dependabot for GitHub Actions and Composer dependency updates.
- Excluded development files (tests, CI config, tooling config) from Composer dist archives.

## v1.0.2 - 2026-06-03

- Added a security policy with supported versions and private vulnerability reporting instructions.

## v1.0.1 - 2026-06-03

- Added post-CI repository dispatch automation using `saedyousef/repository-dispatch`.
- Added a repository-dispatch receiver workflow that builds and uploads a Composer package archive after CI passes.

## v1.0.0 - 2026-05-10

- Added webhook triggering through `$client->webhooks()->trigger()`.
- Added execution fetching and polling through the n8n API.
- Added explicit webhook request body helpers for JSON, text, streams, and empty requests.
- Added lifecycle hooks for webhook requests, execution tracking, polling, and errors.
- Added strict webhook URL validation for absolute `http` and `https` URLs.
- Added PSR-7, PSR-17, and PSR-18 based transport abstractions.

## Release Notes

`v1.0.4` adds package author metadata and allows PHP_CodeSniffer 4 for development. It does not change the runtime API.

`v1.0.3` hardens the CI/CD workflows and adds automated dependency updates. It does not change the runtime API.

`v1.0.2` adds the security policy and vulnerability reporting process. It does not change the runtime API.

`v1.0.1` is a maintenance release focused on CI/CD orchestration. It does not change the runtime API.

`v1.0.0` establishes the initial stable public API. Future breaking changes will be reserved for major releases.
