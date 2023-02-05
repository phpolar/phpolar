<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Routing;

/**
 * Contains route paths and their associated
 * request handlers.
 */
class RouteRegistry
{
    /**
     * @var array<string,AbstractRequestHandler> Associates routes with an action.
     */
    private array $registry = [];

    /**
     * Associates a request handler to a route.
     */
    public function add(string $route, AbstractRequestHandler $handler): void
    {
        $this->registry[$route] = $handler;
    }

    /**
     * Retrieves the registered request handler.
     */
    public function get(string $route): AbstractRequestHandler|RouteNotRegistered
    {
        return $this->registry[$route] ?? new RouteNotRegistered();
    }
}
