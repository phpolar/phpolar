<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

/**
 * Takes care of internal server error handling.
 */
final class ServerErrorMiddleware implements MiddlewareInterface
{
    private bool $hasError = false;

    /**
     * @param Closure(Throwable $e): void $exceptionHandler
     */
    public function __construct(
        private Closure $exceptionHandler,
        private RequestHandlerInterface $serverErrorRequestHandler,
    ) {
        set_exception_handler($this->handleError(...));
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        return $this->hasError === true
            ? $this->serverErrorRequestHandler->handle($request)
            : $handler->handle($request);
    }

    public function hasError(): bool
    {
        return $this->hasError;
    }

    private function handleError(Throwable $e): void
    {
        $this->hasError = true;
        ($this->exceptionHandler)($e);
    }
}
