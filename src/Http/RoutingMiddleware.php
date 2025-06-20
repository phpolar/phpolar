<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum as HttpResponseCode;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Takes care of routing requests to handlers
 */
final class RoutingMiddleware implements MiddlewareInterface
{
    public function __construct(private RequestHandlerInterface $requestHandler) {}

    /**
     * Handle routing a request.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $this->requestHandler->handle($request);
        return $response->getStatusCode() === HttpResponseCode::NotFound->value ? $handler->handle($request) : $response;
    }
}
