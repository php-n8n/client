<?php

declare(strict_types=1);

namespace PhpN8n\Client\Exceptions;

use Psr\Http\Message\ResponseInterface;
use Throwable;

class AuthenticationException extends RequestException
{
    public function __construct(
        string $message = 'n8n API authentication failed.',
        ?ResponseInterface $response = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $response, $previous);
    }
}
