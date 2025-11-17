<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\HttpRequestProcessor\RequestProcessorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Determines if authorization is successful
 * for a given request processor.
 *
 * @codeCoverageIgnore
 *
 * @deprecated Use use RequestAuthorizerInterface instead
 * @link RequestAuthorizerInterface
 */
interface AuthorizationCheckerInterface
{
    /**
     * Returns the request processor when authorization is successful
     * or a PSR-7 HTTP response when it is not.
     */
    public function authorize(
        RequestProcessorInterface $requestProcessor,
        ServerRequestInterface $request,
    ): RequestProcessorInterface | ResponseInterface;
}
