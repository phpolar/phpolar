<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Routing;

use Psr\Container\ContainerInterface;

/**
 * Defines what should be done when a request is received.
 *
 * This is intended to be used to define an
 * action associated with a route.  This MUST, at least, return the content of the
 * response body.  However, any action MAY also be performed.  The implementation MAY or MAY
 * NOT cause side effects.
 */
abstract class AbstractContentDelegate
{
    /**
     * Returns the content of the response body.
     *
     * This MAY also execute any action associated with a given route.
     *
     * @return string The content of the response body.
     *
     * # Route Parameters
     *
     * The framework will pass *route parameters* to this
     * method based on the name in the route.
     *
     * ## Examples
     * ```php
     * $route = `/some/path/{id}`;
     *
     * class PathWithIdDelegate extends AbstractContentDelegate
     * {
     *     // Define route parameters as optional arguments in the child class.
     *     public function getResponseContent(ContainerInterface $container, string $id = ""): string
     *     {
     *         // ...
     *     }
     * }
     *
     *
     * $route = `/some/path/{name}`;
     *
     * class PathWithNameDelegate extends AbstractContentDelegate
     * {
     *     // Define route parameters as optional arguments in the child class.
     *     public function getResponseContent(ContainerInterface $container, string $name = ""): string
     *     {
     *         // ...
     *     }
     * }
     *
     * ```
     */
    abstract public function getResponseContent(ContainerInterface $container): string;
}
