<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\DependencyInjection;

use ArrayAccess;
use Phpolar\Phpolar\Core\ContainerLoader;
use Phpolar\Phpolar\Http\MiddlewareQueueRequestHandler;
use Phpolar\Phpolar\Http\RoutingMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

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

    /**
     * @param ContainerFactoryInterface $containerFac
     * @param ArrayAccess<string,mixed> $containerConfig
     */
    public function __construct(
        ContainerFactoryInterface $containerFac,
        ArrayAccess $containerConfig
    ) {
        $this->container = $containerFac->getContainer($containerConfig);
        (new ContainerLoader())->load($containerConfig, $this->container);
    }

    /**
     * Retrieves the CSRF pre-routing middleware.
     */
    public function getCsrfPreRoutingMiddleware(): MiddlewareInterface
    {
        /**
         * @var MiddlewareInterface $middleware
         */
        $middleware = $this->container->get(DiTokens::CSRF_CHECK_MIDDLEWARE);
        return $middleware;
    }

    /**
     * Retrieves the CSRF post-routing middleware
     */
    public function getCsrfPostRoutingMiddleware(): MiddlewareInterface
    {
        /**
         * @var MiddlewareInterface $middleware
         */
        $middleware = $this->container->get(DiTokens::CSRF_RESPONSE_FILTER_MIDDLEWARE);
        return $middleware;
    }

    /**
     * Retrieves the middleware processing queue
     */
    public function getMiddlewareQueueRequestHandler(): MiddlewareQueueRequestHandler
    {
        /**
         * @var MiddlewareQueueRequestHandler
         */
        $handler = $this->container->get(MiddlewareQueueRequestHandler::class);
        return $handler;
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
