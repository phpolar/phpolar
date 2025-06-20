<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\Routable\RoutableInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface AuthorizationCheckerInterface
{
    public function authorize(
        RoutableInterface $routable,
        ServerRequestInterface $request,
    ): RoutableInterface | ResponseInterface;
}
