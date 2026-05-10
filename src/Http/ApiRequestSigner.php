<?php

declare(strict_types=1);

namespace PhpN8n\Client\Http;

use PhpN8n\Client\Config\ApiConfig;
use Psr\Http\Message\RequestInterface;

final readonly class ApiRequestSigner
{
    public function sign(RequestInterface $request, ApiConfig $config): RequestInterface
    {
        $request = $request->withHeader('Accept', 'application/json');

        if ($config->apiKey() === null || $config->apiKey() === '') {
            return $request;
        }

        return $request->withHeader('X-N8N-API-KEY', $config->apiKey());
    }
}
