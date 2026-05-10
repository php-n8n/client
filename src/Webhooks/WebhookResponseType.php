<?php

declare(strict_types=1);

namespace PhpN8n\Client\Webhooks;

enum WebhookResponseType: string
{
    case None = 'none';
    case Json = 'json';
    case Text = 'text';
}
