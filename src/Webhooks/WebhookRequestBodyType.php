<?php

declare(strict_types=1);

namespace PhpN8n\Client\Webhooks;

enum WebhookRequestBodyType
{
    case Empty;
    case Json;
    case Text;
    case Stream;
}
