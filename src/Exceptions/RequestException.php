<?php

declare(strict_types=1);

namespace PhpN8n\Client\Exceptions;

use Psr\Http\Message\ResponseInterface;
use Throwable;

class RequestException extends N8nException
{
    public function __construct(
        string $message,
        private readonly ?ResponseInterface $response = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $response?->getStatusCode() ?? 0, $previous);
    }

    public function response(): ?ResponseInterface
    {
        return $this->response;
    }
}
