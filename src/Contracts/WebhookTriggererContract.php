<?php

declare(strict_types=1);

namespace PhpN8n\Client\Contracts;

use PhpN8n\Client\Webhooks\Webhook;
use PhpN8n\Client\Webhooks\WebhookRequest;
use PhpN8n\Client\Webhooks\WebhookResponse;

interface WebhookTriggererContract
{
    public function trigger(Webhook $webhook, ?WebhookRequest $request = null): WebhookResponse;
}
