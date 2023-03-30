<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The HTTP Server request handling starting point
 * for the application.
 *
 * This handler is implemented as a
 * [queue-based handler](https://www.php-fig.org/psr/psr-15/meta/#queue-based-request-handler)
 * that processes [PSR-15 middleware](https://www.php-fig.org/psr/psr-15/#22-psrhttpservermiddlewareinterface).
 */
class PrimaryHandler implements RequestHandlerInterface
{
    /**
     * A collection of middleware to
     * be processed in FIFO order.
     *
     * @var MiddlewareInterface[]
     */
    private array $middlewareQueue = [];

    public function __construct(private RequestHandlerInterface $fallbackHandler)
    {
    }

    /**
     * Gets the next middleware from the processing queue.
     */
    private function dequeue(): MiddlewareInterface|null
    {
        return array_shift($this->middlewareQueue);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $nextMiddleware = $this->dequeue();
        if ($nextMiddleware === null) {
            return $this->fallbackHandler->handle($request);
        }
        return $nextMiddleware->process($request, $this);
    }

    /**
     * Load the provided middleware
     * on the queue for processing.
     */
    public function queue(MiddlewareInterface $middleware): void
    {
        $this->middlewareQueue[] = $middleware;
    }
}
