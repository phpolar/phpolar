<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Routing;

/**
 * Defines what should be done when a request is received.
 *
 * This is NOT a PSR-15 request handler.
 */
abstract class AbstractRequestHandler
{
    /**
     * Execute the defined action associated with a given route.
     */
    abstract public function handle(): string;
}
