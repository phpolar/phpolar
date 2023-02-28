<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Routing;

use Psr\Http\Message\ServerRequestInterface;

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
     * Associates a request handler to a request.
     */
    public function add(string $method, string $route, AbstractContentDelegate $handler): void
    {
        if (strtoupper($method) === "GET") {
            $this->registryForGet[$route] = $handler;
            return;
        }
        $this->registryForPost[$route] = $handler;
    }

    /**
     * Retrieves the registered handler for a request.
     */
    public function match(ServerRequestInterface $request): AbstractContentDelegate|RouteNotRegistered
    {
        $method = $request->getMethod();
        $route = $request->getUri()->getPath();
        return strtoupper($method) === "GET" ? ($this->registryForGet[$route] ?? new RouteNotRegistered()) : ($this->registryForPost[$route] ?? new RouteNotRegistered());
    }
}
