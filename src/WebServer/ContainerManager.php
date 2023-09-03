<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer;

use ArrayAccess;
use Phpolar\CsrfProtection\Http\CsrfRequestCheckMiddleware;
use Phpolar\CsrfProtection\Http\CsrfResponseFilterMiddleware;
use Phpolar\Phpolar\Routing\RouteRegistry;
use Phpolar\Phpolar\Routing\RoutingMiddleware;
use Phpolar\Phpolar\WebServer\Http\PrimaryHandler;
use Psr\Container\ContainerInterface;

/**
 * Manages the dependency injection container.
 *
 * Adds dependencies, checks for required dependencies,
 * and handles errors.
 */
final class ContainerManager
{
    /**
     * The PSR-11 dependency injection container.
     */
    private ContainerInterface $container;

    private ContainerLoader $containerLoader;

    /**
     * @param AbstractContainerFactory $containerFac
     * @param ArrayAccess<string,mixed> $containerConfig
     */
    public function __construct(
        AbstractContainerFactory $containerFac,
        ArrayAccess $containerConfig
    ) {
        $this->container = $containerFac->getContainer($containerConfig);
        $this->containerLoader = new ContainerLoader($containerConfig, $this->container);
    }

    /**
     * Retrieves the CSRF pre-routing middleware.
     */
    public function getCsrfPreRoutingMiddleware(): CsrfRequestCheckMiddleware
    {
        /**
         * @var CsrfRequestCheckMiddleware $middleware
         */
        $middleware = $this->container->get(CsrfRequestCheckMiddleware::class);
        return $middleware;
    }

    /**
     * Retrieves the CSRF post-routing middleware
     */
    public function getCsrfPostRoutingMiddleware(): CsrfResponseFilterMiddleware
    {
        /**
         * @var CsrfResponseFilterMiddleware $middleware
         */
        $middleware = $this->container->get(CsrfResponseFilterMiddleware::class);
        return $middleware;
    }

    /**
     * Retrieves the middleware processing queue
     */
    public function getPrimaryHandler(): PrimaryHandler
    {
        /**
         * @var PrimaryHandler
         */
        $handler = $this->container->get(PrimaryHandler::class);
        return $handler;
    }

    /**
     * Adds routes to the container.
     */
    public function loadRoutes(RouteRegistry $routes): void
    {
        $this->containerLoader->loadRoutes($routes);
    }

    /**
     * Retrieves the routing middleware.
     */
    public function getRoutingMiddleware(): RoutingMiddleware
    {
        /**
         * @var RoutingMiddleware $routingMiddleware
         */
        $routingMiddleware = $this->container->get(RoutingMiddleware::class);
        return $routingMiddleware;
    }
}
