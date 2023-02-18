<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer;

use ArrayAccess;
use Phpolar\CsrfProtection\Http\CsrfPostRoutingMiddlewareFactory;
use Phpolar\CsrfProtection\Http\CsrfPreRoutingMiddleware;
use Phpolar\Phpolar\Routing\DefaultRoutingHandler;
use Phpolar\Phpolar\Routing\RouteRegistry;
use Phpolar\Phpolar\WebServer\Http\Error401Handler;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
     * @param AbstractContainerFactory $containerFac
     * @param ArrayAccess<string,mixed> $containerConfig
     */
    public function __construct(
        AbstractContainerFactory $containerFac,
        ArrayAccess $containerConfig
    ) {
        $configurator = new ContainerConfigurator();
        $configurator->configureContainer($containerConfig);
        $this->container = $containerFac->getContainer($containerConfig);
    }

    /**
     * Retrieves the CSRF post-routing middleware.
     */
    public function getCsrfPostRoutingMiddlewareFactory(): CsrfPostRoutingMiddlewareFactory
    {
        /**
         * @var CsrfPostRoutingMiddlewareFactory $factory
         */
        $factory = $this->container->get(CsrfPostRoutingMiddlewareFactory::class);
        return $factory;
    }

    /**
     * Retrieves the CSRF pre-routing middleware.
     */
    public function getCsrfPreRoutingMiddleware(): CsrfPreRoutingMiddleware
    {
        /**
         * @var CsrfPreRoutingMiddleware $middleware
         */
        $middleware = $this->container->get(CsrfPreRoutingMiddleware::class);
        return $middleware;
    }

    /**
     * Retrieves a 401 response error handler.
     */
    public function getErrorHandler(): Error401Handler
    {
        /**
         * @var Error401Handler $handler
         */
        $handler = $this->container->get(Error401Handler::class);
        return $handler;
    }

    /**
     * Retrieves the middleware processing queue
     */
    public function getMiddlewareQueue(): MiddlewareProcessingQueue
    {
        /**
         * @var MiddlewareProcessingQueue
         */
        $middlewareQueue = $this->container->get(MiddlewareProcessingQueue::class);
        return $middlewareQueue;
    }

    /**
     * Retrieves the primary request handler.
     *
     * This is usually the request handler that takes
     * care of routing a successful request.  However,
     * the user can add any PSR-15 request handler to
     * the dependency injection container according to
     * the requirements of the application.
     */
    public function getPrimaryRequestHandler(bool $useRoutes, RouteRegistry $routes): RequestHandlerInterface
    {
        /**
         * @var RequestHandlerInterface $handler
         */
        $handler = $useRoutes === true ? new DefaultRoutingHandler($routes, $this->container) : $this->container->get(WebServer::PRIMARY_REQUEST_HANDLER);
        return $handler;
    }
}
