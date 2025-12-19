<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use Closure;
use Phpolar\Phpolar\DependencyInjection\DiTokens;
use Phpolar\Phpolar\Http\MiddlewareQueueRequestHandler;
use Phpolar\Phpolar\Http\RoutingMiddleware;
use Phpolar\Phpolar\Http\ServerErrorMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Throwable;

/**
 * Represents a web application that handles and responds to HTTP requests.
 */
final class App
{
    private MiddlewareQueueRequestHandler $mainHandler;
    private MiddlewareInterface $routingMiddleware;
    private \Laminas\HttpHandlerRunner\Emitter\EmitterInterface $emitter;
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
        /**
         * @var MiddlewareInterface
         */
        $routingMiddleware = $this->container->get(RoutingMiddleware::class);
        $this->routingMiddleware = $routingMiddleware;
        /**
         * @var \Laminas\HttpHandlerRunner\Emitter\EmitterInterface $emitter
         */
        $emitter = $this->container->get(DiTokens::RESPONSE_EMITTER);
        $this->emitter = $emitter;
        $this->mainHandler = $handler;
    }

    /**
     * Creates a singleton web-based application.
     */
    public static function create(
        ContainerInterface $container,
    ): App {
        return self::$instance ??= new self($container);
    }

    /**
     * Handle and respond to requests from clients.
     */
    public function receive(ServerRequestInterface $request): void
    {
        $this->setupRouting();

        $response = $this->mainHandler->handle($request);
        $this->emitter->emit($response);
    }

    private function queueMiddleware(MiddlewareInterface $middleware): void
    {
        $this->mainHandler->queue($middleware);
    }

    /**
     * Queue the given PSR-15 middleware.
     *
     * @see https://www.php-fig.org/psr/psr-15/#22-psrhttpservermiddlewareinterface
     * for more information on PSR-15 middleware.
     */
    public function use(MiddlewareInterface $middleware): App
    {
        $this->queueMiddleware($middleware);
        return $this;
    }

    /**
     * Configures the application for checking route authorization.
     */
    public function useAuthorization(): App
    {
        /** @var \Phpolar\Phpolar\Http\RequestProcessingHandler */
        $authRoutingHandler = $this->container->get(DiTokens::AUTHENTICATED_ROUTING_HANDLER);
        $this->routingMiddleware = new RoutingMiddleware($authRoutingHandler);
        return $this;
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
     * @param array<string,bool|int|float|string> $sessionOpts
     */
    public function useCsrfMiddleware(
        array $sessionOpts = [
            "cookie_httponly" => true,
            "cookie_samesite" => "Strict",
            "cookie_secure" => true,
            "cookie_path" => true,
            "use_strict_mode" => true,
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
     * Configures global exception/throwable handling.
     *
     * @param Closure(Throwable $e): void $exceptionHandler
     */
    public function useExceptionHandler(
        Closure $exceptionHandler
    ): App {
        /**
         * @var \Psr\Http\Server\RequestHandlerInterface
         */
        $serverErrorHandler = $this->container->get(DiTokens::SERVER_ERROR_HANDLER);
        $this->queueMiddleware(new ServerErrorMiddleware($exceptionHandler, $serverErrorHandler));
        return $this;
    }

    /**
     * Configures the application with associated
     * routes and handlers.
     */
    private function setupRouting(): void
    {
        $this->queueMiddleware($this->routingMiddleware);
    }
}
