<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\Routable\RoutableInterface;
use Phpolar\Routable\RoutableResolverInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Determines if authorization is successful
 * for a given Routable.
 */
final class AuthorizationChecker implements AuthorizationCheckerInterface
{
    public function __construct(
        private readonly RoutableResolverInterface $routableResolver,
        private readonly RequestHandlerInterface $unauthHandler,
    ) {
    }

    /**
     * Returns Routable when authorization is successful
     * and an Unauthorized HTTP response when it is not.
     */
    public function authorize(RoutableInterface $routable, ServerRequestInterface $request): RoutableInterface|ResponseInterface
    {
        $authResult = $this->routableResolver->resolve($routable);

        if ($authResult === false) {
            return $this->unauthHandler->handle($request);
        }

        return $routable;
    }
}
