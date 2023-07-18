<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\ModelResolver\ModelResolverInterface;
use Phpolar\Phpolar\Core\Routing\RouteNotRegistered;
use Phpolar\Phpolar\RoutableInterface;
use Phpolar\Phpolar\RoutableResolverInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionMethod;

/**
 * Handles request routing for the application.
 */
class RoutingHandler implements RequestHandlerInterface
{
    public function __construct(
        private RouteRegistry $routeRegistry,
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
        private ContainerInterface $container,
        private ModelResolverInterface $modelResolver,
        private RoutableResolverInterface $routableResolver,
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
        $matchResult = $this->routeRegistry->match($request);
        return match (true) {
            $matchResult instanceof RouteNotRegistered => $this->responseFactory->createResponse(404, "Not Found"),
            $matchResult instanceof ResolvedRoute => $this->handleResolvedRoute($matchResult),
            default => $this->handleDelegate($matchResult),
        };
    }

    private function getResponse(string $responseContent): ResponseInterface
    {
        $responseStream = $this->streamFactory->createStream($responseContent);
        $response = $this->responseFactory->createResponse();
        return $response->withBody($responseStream);
    }

    private function handleDelegate(RoutableInterface $delegate): ResponseInterface
    {
        if ($this->routableResolver->resolve($delegate) === false) {
            return $this->responseFactory->createResponse(401, "Unauthorized");
        }

        $modelParams = $this->modelResolver->resolve($delegate, "process");
        /**
         * @var string $responseContent
         */
        $responseContent = empty($modelParams) === false ? (new ReflectionMethod($delegate, "process"))->invokeArgs($delegate, array_merge([$this->container], $modelParams)) : $delegate->process($this->container);
        return $this->getResponse($responseContent);
    }

    private function handleResolvedRoute(ResolvedRoute $resolvedRoute): ResponseInterface
    {
        if ($this->routableResolver->resolve($resolvedRoute->delegate) === false) {
            return $this->responseFactory->createResponse(401);
        }

        $reflectionMethod = new ReflectionMethod($resolvedRoute->delegate, "process");
        $args = array_merge([$this->container], $resolvedRoute->routeParamMap->toArray());
        /**
         * @var string $responseContent
         */
        $responseContent = $reflectionMethod->invokeArgs($resolvedRoute->delegate, $args);
        return $this->getResponse($responseContent);
    }
}
