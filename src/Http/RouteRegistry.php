<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use DomainException;
use Psr\Http\Message\ServerRequestInterface;
use Phpolar\Phpolar\RoutableInterface;
use Phpolar\Phpolar\Core\Routing\RouteNotRegistered;
use Phpolar\Phpolar\Core\Routing\RouteParamMap;

use const Phpolar\Phpolar\Core\Routing\ROUTE_PARAM_PATTERN;

/**
 * Contains route paths and their associated
 * request handlers.
 */
class RouteRegistry
{
    /**
     * @var array<string,RoutableInterface> Stores actions for `GET` requests.
     */
    private array $registryForGet = [];

    /**
     * @var array<string,RoutableInterface> Stores actions for `POST` requests.
     */
    private array $registryForPost = [];

    private bool $containsParamRoutes = false;

    /**
     * Associates a request handler to a request.
     */
    public function add(string $method, string $route, RoutableInterface $handler): void
    {
        $this->containsParamRoutes = $this->containsParamRoutes || preg_match(ROUTE_PARAM_PATTERN, $route) === 1;
        if (strtoupper($method) === "GET") {
            $this->registryForGet[$route] = $handler;
            return;
        }
        if (strtoupper($method) === "POST") {
            $this->registryForPost[$route] = $handler;
            return;
        }
        throw new DomainException(sprintf("%s is not supported", $method));
    }

    /**
     * Retrieves the registered handler for a request.
     */
    public function match(ServerRequestInterface $request): RoutableInterface | ResolvedRoute | RouteNotRegistered
    {
        $method = $request->getMethod();
        $route = $request->getUri()->getPath();
        return strtoupper($method) === "GET" ? $this->matchGetRoute($route) : $this->matchPostRoute($route);
    }

    private function matchGetRoute(string $route): RoutableInterface | ResolvedRoute | RouteNotRegistered
    {
        return $this->registryForGet[$route] ?? ($this->containsParamRoutes === false ? new RouteNotRegistered() : $this->matchAnyParameterizedRoute($this->registryForGet, $route));
    }

    private function matchPostRoute(string $route): RoutableInterface | ResolvedRoute | RouteNotRegistered
    {
        return $this->registryForPost[$route] ?? ($this->containsParamRoutes === false ? new RouteNotRegistered() : $this->matchAnyParameterizedRoute($this->registryForPost, $route));
    }

    /**
     * @param array<string,RoutableInterface> $registry
     * @param string $path
     */
    private function matchAnyParameterizedRoute(array $registry, string $path): ResolvedRoute | RouteNotRegistered
    {
        // Reindex the result. See https://www.php.net/manual/en/function.array-filter.php
        $matched = array_values(
            array_filter(
                array_keys($registry),
                static fn (string $registeredRoute) => self::partsMatch($registeredRoute, $path),
            )
        );
        return empty($matched) === true ? new RouteNotRegistered() : new ResolvedRoute($registry[$matched[0]], new RouteParamMap($matched[0], $path));
    }

    private static function partsMatch(string $registeredRoute, string $path): bool
    {
        $pathParts = explode("/", ltrim($path, "/"));
        $routeParts = explode("/", ltrim($registeredRoute, "/"));
        $routePartsCnt = count($routeParts);
        if ($routePartsCnt !== count($pathParts)) {
            return false;
        }
        return count(
            array_filter(
                array_combine($routeParts, $pathParts),
                static fn (string $pathPart, string $routePart) => $routePart === $pathPart || preg_match(ROUTE_PARAM_PATTERN, $routePart) === 1,
                ARRAY_FILTER_USE_BOTH,
            )
        ) === count($routeParts);
    }
}
