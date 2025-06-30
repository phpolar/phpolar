<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\HttpRequestProcessor\RequestProcessorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface AuthorizationCheckerInterface
{
    public function authorize(
        RequestProcessorInterface $routable,
        ServerRequestInterface $request,
    ): RequestProcessorInterface | ResponseInterface;
}
