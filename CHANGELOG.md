# Changelog

All notable changes to `php-n8n/client` will be documented in this file.

Full documentation:
https://php-n8n.com

This project follows [Semantic Versioning](https://semver.org/).

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

`v1.0.2` adds the security policy and vulnerability reporting process. It does not change the runtime API.

`v1.0.1` is a maintenance release focused on CI/CD orchestration. It does not change the runtime API.

`v1.0.0` establishes the initial stable public API. Future breaking changes will be reserved for major releases.
