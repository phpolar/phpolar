<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\HttpRequestProcessor\RequestProcessorInterface;
use Phpolar\HttpRequestProcessor\RequestProcessorResolverInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Determines if authorization is successful
 * for a given request processor.
 */
final class AuthorizationChecker implements AuthorizationCheckerInterface
{
    public function __construct(
        private readonly RequestProcessorResolverInterface $routableResolver,
        private readonly RequestHandlerInterface $unauthHandler,
    ) {}

    /**
     * Returns request processor when authorization is successful
     * and an Unauthorized HTTP response when it is not.
     */
    public function authorize(RequestProcessorInterface $routable, ServerRequestInterface $request): RequestProcessorInterface|ResponseInterface
    {
        $authResult = $this->routableResolver->resolve($routable);

        if ($authResult === false) {
            return $this->unauthHandler->handle($request);
        }

        return $routable;
    }
}
