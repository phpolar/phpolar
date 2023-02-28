<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer;

use ArrayAccess;
use Phpolar\Extensions\HttpResponse\ResponseExtension;
use Phpolar\Phpolar\Routing\RouteRegistry;
use Phpolar\Phpolar\WebServer\Http\MiddlewareQueueInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Represents a server that handles and responds to request.
 */
final class WebServer
{
    public const ERROR_HANDLER_401 = "ERROR_HANDLER_401";
    public const ERROR_HANDLER_404 = "ERROR_HANDLER_404";

    private ContainerManager $containerManager;

    private RequestHandlerInterface&MiddlewareQueueInterface $primaryHandler;

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
        $this->primaryHandler = $this->containerManager->getPrimaryHandler();
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
     *
     * If `useRoutes` is not called before this method,
     * a 401 "Not Found" response will be produced.
     */
    public function receive(ServerRequestInterface $request): void
    {
        $response = $this->primaryHandler->handle($request);
        ResponseExtension::extend($response)->send();
    }

    private function queueMiddleware(MiddlewareInterface $middleware): void
    {
        $this->primaryHandler->queue($middleware);
    }

    /**
     * Configures a session.
     *
     * @param array<string,mixed> $options
     */
    public function useSession(
        array $options = [
            "cookie_httponly" => true,
            "cookie_samesite" => "Strict",
            "cookie_secure" => true,
            "cookie_path" => true,
            "use_strict_mode" => true,
            "referer_check" => true,
        ]
    ): WebServer {
        session_status() !== PHP_SESSION_ACTIVE && session_start($options); // @codeCoverageIgnore
        return $this;
    }

    /**
     * Configures the server for CSRF attack mitigation.
     *
     * The server will not process the request if the
     * CSRF check fails.  The current response
     * will be set up for CSRF detection.
     *
     * Must be called directly before the
     * `useRoutes` method is called.
     *
     * @param array<string,bool|int|float|string> $sessionOpts
     *
     * @throws WebServerConfigurationException
     */
    public function useCsrfMiddleware(
        array $sessionOpts = [
            "cookie_httponly" => true,
            "cookie_samesite" => "Strict",
            "cookie_secure" => true,
            "cookie_path" => true,
            "use_strict_mode" => true,
            "referer_check" => true,
        ]
    ): WebServer {
        $this->useSession($sessionOpts);
        $csrfPreRouting = $this->containerManager->getCsrfPreRoutingMiddleware();
        $csrfPostRouting = $this->containerManager->getCsrfPostRoutingMiddleware();
        $this->queueMiddleware($csrfPreRouting);
        $this->queueMiddleware($csrfPostRouting);
        return $this;
    }

    /**
     * Configures the web server with associated
     * routes and handlers.
     */
    public function useRoutes(RouteRegistry $routes): WebServer
    {
        $this->containerManager->loadRoutes($routes);
        $routingMiddleware = $this->containerManager->getRoutingMiddleware();
        $this->queueMiddleware($routingMiddleware);
        return $this;
    }
}
