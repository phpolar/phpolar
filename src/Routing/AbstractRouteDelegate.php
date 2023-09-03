<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Routing;

/**
 * Defines what should be done when a request is received.
 *
 * This is expected to return the content of the
 * response body.
 *
 * This is **NOT** a PSR-15 request handler.
 */
abstract class AbstractRouteDelegate
{
    /**
     * Execute the defined action associated with a given route.
     *
     * @return string The content of the response body.
     */
    abstract public function handle(): string;
}
