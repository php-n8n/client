<?php

declare(strict_types=1);

namespace PhpN8n\Client\Contracts;

use PhpN8n\Client\Webhooks\WebhookResponse;
use Psr\Http\Message\ResponseInterface;

interface WebhookResponseResolverContract
{
    public function resolve(ResponseInterface $response): WebhookResponse;
}
