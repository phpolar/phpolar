<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\ModelResolver\ModelResolverInterface;
use Phpolar\Phpolar\Core\Routing\RouteNotRegistered;
use Phpolar\Phpolar\Http\ErrorHandler;
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
        private ErrorHandler $errorHandler,
        private ContainerInterface $container,
        private ModelResolverInterface $modelResolver,
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
            $matchResult instanceof RouteNotRegistered => $this->errorHandler->handle($request),
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
        $modelParams = $this->modelResolver->resolve($delegate, "process");
        /**
         * @var string $responseContent
         */
        $responseContent = empty($modelParams) === false ? (new ReflectionMethod($delegate, "process"))->invokeArgs($delegate, array_merge([$this->container], $modelParams)) : $delegate->process($this->container);
        return $this->getResponse($responseContent);
    }

    private function handleResolvedRoute(ResolvedRoute $resolvedRoute): ResponseInterface
    {
        $reflectionMethod = new ReflectionMethod($resolvedRoute->delegate, "process");
        $args = array_merge([$this->container], $resolvedRoute->routeParamMap->toArray());
        /**
         * @var string $responseContent
         */
        $responseContent = $reflectionMethod->invokeArgs($resolvedRoute->delegate, $args);
        return $this->getResponse($responseContent);
    }
}
