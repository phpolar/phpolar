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
 *
 * @codeCoverageIgnore
 *
 * @suppress PhanDeprecatedInterface
 * @deprecated
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
    public function authorize(RequestProcessorInterface $requestProcessor, ServerRequestInterface $request): RequestProcessorInterface|ResponseInterface
    {
        $result = $this->routableResolver->resolve($requestProcessor);

        if ($result === false) {
            return $this->unauthHandler->handle($request);
        }

        return $result;
    }
}
