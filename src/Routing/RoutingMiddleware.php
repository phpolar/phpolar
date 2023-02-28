<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Routing;

use Phpolar\HttpCodes\ResponseCode;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Takes care of routing requests to handlers
 */
class RoutingMiddleware implements MiddlewareInterface
{
    public function __construct(private RoutingHandler $requestHandler)
    {
    }

    /**
     * Handle routing a request.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $this->requestHandler->handle($request);
        return $response->getStatusCode() === ResponseCode::NOT_FOUND ? $handler->handle($request) : $response;
    }
}
