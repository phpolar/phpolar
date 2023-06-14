<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Psr\Container\ContainerInterface;

/**
 * Defines what should be done when a request is received.
 *
 * This is intended to be used to define an
 * action associated with a route.  This MUST, at least, return the content of the
 * response body.  However, any action MAY also be performed.  The implementation MAY or MAY
 * NOT cause side effects.
 */
interface RoutableInterface
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
     * class PathWithIdDelegate implements RoutableInterface
     * {
     *     // Define route parameters as optional arguments in the child class.
     *     public function process(ContainerInterface $container, string $id = ""): string
     *     {
     *         // ...
     *     }
     * }
     *
     *
     * $route = `/some/path/{name}`;
     *
     * class PathWithNameDelegate implements RoutableInterface
     * {
     *     // Define route parameters as optional arguments in the child class.
     *     public function process(ContainerInterface $container, string $name = ""): string
     *     {
     *         // ...
     *     }
     * }
     *
     * ```
     */
    public function process(ContainerInterface $container): string;
}
