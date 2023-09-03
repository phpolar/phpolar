<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Routing;

use Phpolar\HttpCodes\ResponseCode;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Handles request routing for the application.
 */
final class DefaultRoutingHandler implements RequestHandlerInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
        private RouteRegistry $routeRegistry,
    ) {
    }

    /**
     * Locates and executes the registered route handler.
     *
     * If a handler for the route cannot be located,
     * a "Not Found" response will be returned.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $request->getUri()->getPath();
        $handler = $this->routeRegistry->get($route);
        if ($handler instanceof RouteNotRegistered) {
            return $this->responseFactory->createResponse(ResponseCode::NOT_FOUND, "Not Found");
        }
        $responseContent = $handler->handle();
        $responseStream = $this->streamFactory->createStream($responseContent);
        $response = $this->responseFactory->createResponse();
        return $response->withBody($responseStream);
    }
}