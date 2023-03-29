<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer;

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

    private static RequestHandlerInterface&MiddlewareQueueInterface $primaryHandler;
    private static ContainerManager $containerManager;
    private static ?WebServer $instance = null;


    /**
     * Prevent creation of multiple instances.
     */
    private function __construct(
        ContainerManager $containerManager,
    ) {
        self::$containerManager = $containerManager;
        self::$primaryHandler = self::$containerManager->getPrimaryHandler();
    }

    /**
     * Creates a singleton web server application.  This framework targets the
     * *stateless, single-threaded, server-side application use case*.  Therefore,
     * only a single instance is created on each request.
     */
    public static function createApp(
        ContainerManager $containerManager,
    ): WebServer {
        return self::$instance = self::$instance ?? new self($containerManager);
    }

    /**
     * Handle and respond to requests from clients.
     *
     * If `useRoutes` is not called before this method,
     * a 401 "Not Found" response will be produced.
     */
    public function receive(ServerRequestInterface $request): void
    {
        $response = self::$primaryHandler->handle($request);
        ResponseExtension::extend($response)->send();
    }

    private static function queueMiddleware(MiddlewareInterface $middleware): void
    {
        self::$primaryHandler->queue($middleware);
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
        $csrfPreRouting = self::$containerManager->getCsrfPreRoutingMiddleware();
        $csrfPostRouting = self::$containerManager->getCsrfPostRoutingMiddleware();
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
        self::$containerManager->loadRoutes($routes);
        $routingMiddleware = self::$containerManager->getRoutingMiddleware();
        self::queueMiddleware($routingMiddleware);
        return $this;
    }
}
