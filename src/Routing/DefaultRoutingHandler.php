<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Routing;

use Phpolar\HttpCodes\ResponseCode;
use Phpolar\Phpolar\WebServer\Http\ErrorHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Handles request routing for the application.
 */
class DefaultRoutingHandler implements RequestHandlerInterface
{
    public function __construct(
        private RouteRegistry $routeRegistry,
        private ContainerInterface $container,
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
        /**
         * @var ResponseFactoryInterface $responseFactory
         */
        $responseFactory = $this->container->get(ResponseFactoryInterface::class);
        /**
         * @var StreamFactoryInterface $streamFactory
         */
        $streamFactory = $this->container->get(StreamFactoryInterface::class);
        $route = $request->getUri()->getPath();
        $handler = strtoupper($request->getMethod()) === "GET" ? $this->routeRegistry->fromGet($route) : $this->routeRegistry->fromPost($route);
        if ($handler instanceof RouteNotRegistered) {
            return (new ErrorHandler(ResponseCode::NOT_FOUND, "Not Found", $this->container))->handle($request);
        }
        $responseContent = $handler->getResponseContent($this->container);
        $responseStream = $streamFactory->createStream($responseContent);
        $response = $responseFactory->createResponse();
        return $response->withBody($responseStream);
    }
}
