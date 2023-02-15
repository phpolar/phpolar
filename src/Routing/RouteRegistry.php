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
     * @var array<string,AbstractRouteDelegate> Stores actions for `GET` requests.
     */
    private array $registryForGet = [];

    /**
     * @var array<string,AbstractRouteDelegate> Stores actions for `POST` requests.
     */
    private array $registryForPost = [];

    /**
     * Associates a request handler to a `GET` request.
     */
    public function addGet(string $route, AbstractRouteDelegate $handler): void
    {
        $this->registryForGet[$route] = $handler;
    }

    /**
     * Associates a request handler to a `POST` request.
     */
    public function addPost(string $route, AbstractRouteDelegate $handler): void
    {
        $this->registryForPost[$route] = $handler;
    }

    /**
     * Retrieves the registered handler for a `GET` request.
     */
    public function fromGet(string $route): AbstractRouteDelegate|RouteNotRegistered
    {
        return $this->registryForGet[$route] ?? new RouteNotRegistered();
    }

    /**
     * Retrieves the registered handler for a `POST` request.
     */
    public function fromPost(string $route): AbstractRouteDelegate|RouteNotRegistered
    {
        return $this->registryForPost[$route] ?? new RouteNotRegistered();
    }
}
