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
final class MiddlewareQueueRequestHandler implements RequestHandlerInterface
{
    /**
     * A collection of middleware to
     * be processed in FIFO order.
     *
     * @var MiddlewareInterface[]
     */
    private array $middlewareQueue = [];


    public function __construct(
        private RequestHandlerInterface $fallbackHandler,
        private ?ServerErrorMiddleware $serverErrorMiddleware = null,
    ) {}

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
        if ($nextMiddleware instanceof ServerErrorMiddleware) {
            $this->serverErrorMiddleware = $nextMiddleware;
        }
        if ($this->serverErrorMiddleware !== null && $this->serverErrorMiddleware->hasError() === true) {
            return $this->serverErrorMiddleware->process($request, $this);
        }
        return $nextMiddleware->process($request, $this);
    }

    public function hasError(): bool
    {
        if ($this->serverErrorMiddleware === null) {
            return false;
        }
        return $this->serverErrorMiddleware->hasError();
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
