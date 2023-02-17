<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer;

use ArrayAccess;
use Phpolar\Extensions\HttpResponse\ResponseExtension;
use Phpolar\Phpolar\Routing\DefaultRoutingHandler;
use Phpolar\Phpolar\Routing\RouteRegistry;
use Phpolar\Phpolar\WebServer\Http\Error401Handler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Represents a server that handles and responds to request.
 */
final class WebServer
{
    public const PRIMARY_REQUEST_HANDLER = "PRIMARY_REQUEST_HANDLER";

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

    private bool $useRoutes = false;

    private ContainerManager $containerManager;

    /**
     * Prevent creation of multiple instances.
     *
     * @param ContainerInterface&ArrayAccess<string,mixed> $container
     */
    private function __construct(private ContainerInterface & ArrayAccess $container)
    {
        $this->containerManager = new ContainerManager($container);
        $this->containerManager->setUpContainer();
        $this->containerManager->checkRequiredDeps();
        /**
         * @var MiddlewareProcessingQueue
         */
        $middlewareQueue = $this->container->get(MiddlewareProcessingQueue::class);
        $this->middlewareQueue = $middlewareQueue;
    }

    /**
     * Creates a singleton server.
     *
     * @param ContainerInterface&ArrayAccess<string,mixed> $container
     */
    public static function createApp(ContainerInterface & ArrayAccess $container): WebServer
    {
        return new self($container);
    }

    /**
     * Handle and respond to requests from clients
     */
    public function receive(ServerRequestInterface $request): void
    {
        /**
         * @var \Psr\Http\Server\RequestHandlerInterface $primaryHandler
         */
        $primaryHandler = $this->useRoutes === true ? new DefaultRoutingHandler($this->routes, $this->container) : $this->container->get(self::PRIMARY_REQUEST_HANDLER);
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
        $this->containerManager->checkRequiredCsrfDeps();
        $csrfPreRouting = $this->containerManager->getCsrfPreRoutingMiddleware();
        $csrfPostRouting = $this->containerManager->getCsrfPostRoutingMiddlewareFactory();
        /**
         * @var Error401Handler
         */
        $errorHandler = $this->container->get(Error401Handler::class);
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
        $this->useRoutes = true;
        return $this;
    }
}
