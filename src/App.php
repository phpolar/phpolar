<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use Phpolar\Extensions\HttpResponse\ResponseExtension;
use Phpolar\Phpolar\DependencyInjection\DiTokens;
use Phpolar\Phpolar\Http\MiddlewareQueueRequestHandler;
use Phpolar\Phpolar\Http\RoutingMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Represents an web application that handles and responds to HTTP requests.
 */
final class App
{
    private MiddlewareQueueRequestHandler $mainHandler;
    private static ?App $instance = null;


    /**
     * Prevent creation of multiple instances.
     */
    private function __construct(
        private ContainerInterface $container,
    ) {
        /**
         * @var MiddlewareQueueRequestHandler $handler
         */
        $handler = $this->container->get(MiddlewareQueueRequestHandler::class);
        $this->mainHandler = $handler;
    }

    /**
     * Creates a singleton web server application.  This framework targets the
     * *stateless, single-threaded, server-side application use case*.  Therefore,
     * only a single instance is created on each request.
     */
    public static function create(
        ContainerInterface $container,
    ): App {
        return self::$instance ??= new self($container);
    }

    /**
     * Handle and respond to requests from clients.
     *
     * If `useRoutes` is not called before this method,
     * a 401 "Not Found" response will be produced.
     */
    public function receive(ServerRequestInterface $request): void
    {
        $this->setupRouting();
        $response = $this->mainHandler->handle($request);
        ResponseExtension::extend($response)->send();
    }

    private function queueMiddleware(MiddlewareInterface $middleware): void
    {
        $this->mainHandler->queue($middleware);
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
    ): App {
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
    ): App {
        $this->useSession($sessionOpts);
        /**
         * @var MiddlewareInterface $csrfPreRouting
         */
        $csrfPreRouting = $this->container->get(DiTokens::CSRF_CHECK_MIDDLEWARE);
        /**
         * @var MiddlewareInterface $csrfPostRouting
         */
        $csrfPostRouting = $this->container->get(DiTokens::CSRF_RESPONSE_FILTER_MIDDLEWARE);
        $this->queueMiddleware($csrfPreRouting);
        $this->queueMiddleware($csrfPostRouting);
        return $this;
    }

    /**
     * Configures the web server with associated
     * routes and handlers.
     */
    public function setupRouting(): void
    {
        /**
         * @var MiddlewareInterface $routingMiddleware
         */
        $routingMiddleware = $this->container->get(RoutingMiddleware::class);
        $this->queueMiddleware($routingMiddleware);
    }
}
