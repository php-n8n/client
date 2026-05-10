# Changelog

All notable changes to `php-n8n/client` will be documented in this file.

Full documentation:
https://php-n8n.com

This project follows [Semantic Versioning](https://semver.org/).

## v1.0.0 - Unreleased

- Added webhook triggering through `$client->webhooks()->trigger()`.
- Added execution fetching and polling through the n8n API.
- Added explicit webhook request body helpers for JSON, text, streams, and empty requests.
- Added lifecycle hooks for webhook requests, execution tracking, polling, and errors.
- Added strict webhook URL validation for absolute `http` and `https` URLs.
- Added PSR-7, PSR-17, and PSR-18 based transport abstractions.

## Release Notes

The first tagged release is expected to be `v1.0.0`.

This release establishes the initial stable public API. Future breaking changes will be reserved for major releases.
