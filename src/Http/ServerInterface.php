<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum as HttpResponseCode;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Declares the capabilities of the resource server.
 */
interface ServerInterface
{
    /**
     * Attempts to locate an object associated with a given route.
     *
     * The object defines an action that will be executed for
     * HTTP requests that match the associated route.
     */
    public function findTarget(ServerRequestInterface $request): Target | HttpResponseCode;
}
