<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer;

use Phpolar\CsrfProtection\Http\CsrfPostRoutingMiddlewareFactory;
use Phpolar\CsrfProtection\Http\CsrfPreRoutingMiddleware;
use Phpolar\Extensions\HttpResponse\ResponseExtension;
use Phpolar\Phpolar\WebServer\Http\Error401Handler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Represents a server that handles and responds to request.
 */
final class WebServer
{
    public const PRIMARY_REQUEST_HANDLER = "PRIMARY_REQUEST_HANDLER";

    private MiddlewareProcessingQueue $middlewareQueue;

    private RequestHandlerInterface $primaryHandler;

    /**
     * Dependencies/services required
     * by the web server.
     *
     * @var string[]
     */
    private const REQUIRED_DEPS = [
        MiddlewareProcessingQueue::class,
        self::PRIMARY_REQUEST_HANDLER,
        Error401Handler::class,
    ];

    /**
     * CSRF dependencies required
     * by the web server.
     *
     * @var string[]
     */
    private const REQUIRED_CSRF_DEPS = [
        CsrfPreRoutingMiddleware::class,
        CsrfPostRoutingMiddlewareFactory::class,
    ];

    /**
     * Prevent creation of multiple instances
     */
    private function __construct(private ContainerInterface $container)
    {
        self::checkContainer($container, self::REQUIRED_DEPS);
        /**
         * @var MiddlewareProcessingQueue
         */
        $middlewareQueue = $this->container->get(MiddlewareProcessingQueue::class);
        /**
         * @var RequestHandlerInterface
         */
        $errorHandler = $this->container->get(self::PRIMARY_REQUEST_HANDLER);
        $this->middlewareQueue = $middlewareQueue;
        $this->primaryHandler = $errorHandler;
    }

    /**
     * @param ContainerInterface $container
     * @param string[] $depsToCheck
     * @throws WebServerConfigurationException
     */
    private static function checkContainer(ContainerInterface $container, array $depsToCheck): void
    {
        array_walk(
            $depsToCheck,
            static fn (string $dep) => $container->has($dep)
                || throw new WebServerConfigurationException(
                    sprintf(
                        "Required dependency %s has not been added to the container.",
                        $dep
                    )
                )
        );
    }

    /**
     * Creates a singleton server.
     */
    public static function createApp(ContainerInterface $container): WebServer
    {
        return new self($container);
    }

    /**
     * Handle and respond to requests from clients
     */
    public function receive(ServerRequestInterface $request): void
    {
        $result = $this->middlewareQueue->dequeuePreRoutingMiddleware($request);
        if ($result instanceof AbortProcessingRequest) {
            return;
        }
        $routingResponse = $this->primaryHandler->handle($request);
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
        self::checkContainer($this->container, self::REQUIRED_CSRF_DEPS);
        /**
         * @var CsrfPreRoutingMiddleware
         */
        $csrfPreRouting = $this->container->get(CsrfPreRoutingMiddleware::class);
        /**
         * @var CsrfPostRoutingMiddlewareFactory
         */
        $csrfPostRouting = $this->container->get(CsrfPostRoutingMiddlewareFactory::class);
        /**
         * @var Error401Handler
         */
        $errorHandler = $this->container->get(Error401Handler::class);
        $this->middlewareQueue->addCsrfMiddleware($csrfPreRouting, $csrfPostRouting, $errorHandler);
        return $this;
    }
}
