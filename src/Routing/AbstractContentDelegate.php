<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Routing;

use Psr\Container\ContainerInterface;

/**
 * Defines what should be done when a request is received.
 *
 * This is expected to return the content of the
 * response body.
 */
abstract class AbstractContentDelegate
{
    /**
     * Returns the content of the response body.
     *
     * This may also execute any action associated with a given route.
     *
     * @return string The content of the response body.
     */
    abstract public function getResponseContent(ContainerInterface $container): string;
}
