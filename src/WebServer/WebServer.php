<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer;

use ArrayAccess;
use Phpolar\Extensions\HttpResponse\ResponseExtension;
use Phpolar\Phpolar\Routing\RouteRegistry;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Represents a server that handles and responds to request.
 */
final class WebServer
{
    public const PRIMARY_REQUEST_HANDLER = "PRIMARY_REQUEST_HANDLER";

    public const ERROR_HANDLER_401 = "ERROR_HANDLER_401";

    public const ERROR_HANDLER_404 = "ERROR_HANDLER_404";

    private MiddlewareProcessingQueue $middlewareQueue;

    /**
     * A lookup table used to
     * route requests to handlers.
     *
     * A custom routing handler can
     * be provided, in which case
     * this will likely be ignored.
     */
    private RouteRegistry $routes;

    private bool $shouldUseRoutes = false;

    private ContainerManager $containerManager;

    /**
     * Prevent creation of multiple instances.
     *
     * @param AbstractContainerFactory $containerFac
     * @param ArrayAccess<string,mixed> $containerConfig
     */
    private function __construct(
        AbstractContainerFactory $containerFac,
        ArrayAccess $containerConfig,
    ) {
        $this->containerManager = new ContainerManager($containerFac, $containerConfig);
        $this->middlewareQueue = $this->containerManager->getMiddlewareQueue();
        $this->routes = new RouteRegistry();
    }

    /**
     * Creates a singleton web server application.  This framework targets the
     * *stateless, single-threaded, server-side application use case*.  Therefore,
     * only a single instance is created on each request.  If the provided
     * factory used to create the dependency injection container is stateless,
     * caching this instance should be considered for performance reasons.
     *
     * @param AbstractContainerFactory $containerFactory Adds support for configuring
     * a **PSR-11** dependency injection container before the app is initialized, afterwards,
     * or both.
     *
     * @param ArrayAccess<string,mixed> $containerConfig The framework will configure some
     * services/dependencies after the application is initialized.
     */
    public static function createApp(
        AbstractContainerFactory $containerFactory,
        ArrayAccess $containerConfig,
    ): WebServer {
        return new self($containerFactory, $containerConfig);
    }

    /**
     * Handle and respond to requests from clients.
     */
    public function receive(ServerRequestInterface $request): void
    {
        $primaryHandler = $this->containerManager->getPrimaryRequestHandler($this->shouldUseRoutes, $this->routes);
        $result = $this->middlewareQueue->dequeuePreRoutingMiddleware($request);
        if ($result instanceof AbortProcessingRequest) {
            return;
        }
        $routingResponse = $primaryHandler->handle($request);
        $finalResponse = $this->middlewareQueue->dequeuePostRoutingMiddleware($request, $routingResponse);
        ResponseExtension::extend($finalResponse)->send();
    }

    /**
     * Configures the server for CSRF attack mitigation.
     *
     * The server will not process the request if the
     * CSRF check fails.  The current response
     * will be set up for CSRF detection.
     *
     * @throws WebServerConfigurationException
     */
    public function useCsrfMiddleware(): WebServer
    {
        $csrfPreRouting = $this->containerManager->getCsrfPreRoutingMiddleware();
        $csrfPostRouting = $this->containerManager->getCsrfPostRoutingMiddlewareFactory();
        $errorHandler = $this->containerManager->get401ErrorHandler();
        $this->middlewareQueue->addCsrfMiddleware($csrfPreRouting, $csrfPostRouting, $errorHandler);
        return $this;
    }

    /**
     * Configures the web server with associated
     * routes and handlers.
     */
    public function useRoutes(RouteRegistry $routes): WebServer
    {
        $this->routes = $routes;
        $this->shouldUseRoutes = true;
        return $this;
    }
}
