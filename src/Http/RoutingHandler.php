<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\ModelResolver\ModelResolverInterface;
use Phpolar\Phpolar\Core\Routing\RouteNotRegistered;
use Phpolar\Routable\RoutableInterface;
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
        private RouteMap $routeRegistry,
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
        private ContainerInterface $container,
        private ModelResolverInterface $modelResolver,
        private AuthorizationChecker $authChecker,
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
            $matchResult instanceof ResolvedRoute => $this->handleResolvedRoute($matchResult, $request),
            default => $this->handleDelegate($matchResult, $request),
        };
    }

    private function handleDelegate(RoutableInterface $routable, ServerRequestInterface $request): ResponseInterface
    {
        $result = $this->authChecker->check($routable, $request);

        if ($result instanceof ResponseInterface) {
            return $result;
        }

        $authorizedDelegate = $result;

        $modelParams = $this->modelResolver->resolve($authorizedDelegate, "process");
        /**
         * @var string $responseContent
         */
        $responseContent = empty($modelParams) === false ? (new ReflectionMethod($authorizedDelegate, "process"))->invokeArgs($authorizedDelegate, array_merge([$this->container], $modelParams)) : $authorizedDelegate->process($this->container);

        $responseStream = $this->streamFactory->createStream($responseContent);
        $response = $this->responseFactory->createResponse();
        return $response->withBody($responseStream);
    }

    private function handleResolvedRoute(ResolvedRoute $resolvedRoute, ServerRequestInterface $request): ResponseInterface
    {
        $result = $this->authChecker->check($resolvedRoute->delegate, $request);

        if ($result instanceof ResponseInterface) {
            return $result;
        }

        $resolvedRoute->delegate = $result;

        $reflectionMethod = new ReflectionMethod($resolvedRoute->delegate, "process");
        $args = array_merge([$this->container], $resolvedRoute->routeParamMap->toArray());
        /**
         * @var string $responseContent
         */
        $responseContent = $reflectionMethod->invokeArgs($resolvedRoute->delegate, $args);

        $responseStream = $this->streamFactory->createStream($responseContent);
        $response = $this->responseFactory->createResponse();
        return $response->withBody($responseStream);
    }
}
