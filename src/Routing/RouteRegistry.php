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
     * @var array<string,AbstractContentDelegate> Stores actions for `GET` requests.
     */
    private array $registryForGet = [];

    /**
     * @var array<string,AbstractContentDelegate> Stores actions for `POST` requests.
     */
    private array $registryForPost = [];

    /**
     * Associates a request handler to a `GET` request.
     */
    public function addGet(string $route, AbstractContentDelegate $handler): void
    {
        $this->registryForGet[$route] = $handler;
    }

    /**
     * Associates a request handler to a `POST` request.
     */
    public function addPost(string $route, AbstractContentDelegate $handler): void
    {
        $this->registryForPost[$route] = $handler;
    }

    /**
     * Retrieves the registered handler for a `GET` request.
     */
    public function fromGet(string $route): AbstractContentDelegate|RouteNotRegistered
    {
        return $this->registryForGet[$route] ?? new RouteNotRegistered();
    }

    /**
     * Retrieves the registered handler for a `POST` request.
     */
    public function fromPost(string $route): AbstractContentDelegate|RouteNotRegistered
    {
        return $this->registryForPost[$route] ?? new RouteNotRegistered();
    }
}
