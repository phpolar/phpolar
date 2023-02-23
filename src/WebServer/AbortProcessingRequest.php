<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer;

use Psr\Http\Message\ResponseInterface;

/**
 * Use to notify the web server that the request shall not be processed
 */
final class AbortProcessingRequest
{
    public function __construct(private ResponseInterface $response)
    {
    }

    /**
     * Retrieves the response.
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
