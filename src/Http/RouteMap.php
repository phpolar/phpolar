<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Psr\Http\Message\ServerRequestInterface;
use Phpolar\Phpolar\Core\Routing\RouteNotRegistered;
use Phpolar\Phpolar\Core\Routing\RouteParamMap;
use Phpolar\PropertyInjectorContract\PropertyInjectorInterface;
use Phpolar\Routable\RoutableInterface;
use Phpolar\RoutableFactory\RoutableFactoryInterface;

use const Phpolar\Phpolar\Core\Routing\ROUTE_PARAM_PATTERN;

/**
 * Contains route paths associated with target objects.
 * The target objects define what should happen when a
 * request is mapped to the given route.
 */
class RouteMap
{
    public function __construct(private PropertyInjectorInterface $propertyInjector)
    {
    }

    /**
     * @var array<string,RoutableInterface|RoutableFactoryInterface> Stores actions for `GET` requests.
     */
    private array $registryForGet = [];

    /**
     * @var array<string,RoutableInterface|RoutableFactoryInterface> Stores actions for `POST` requests.
     */
    private array $registryForPost = [];

    private bool $containsParamRoutes = false;

    /**
     * Associates a request method, route and a target object.
     */
    public function add(RequestMethods $method, string $route, RoutableInterface | RoutableFactoryInterface $entry): void
    {
        $this->containsParamRoutes = $this->containsParamRoutes || preg_match(ROUTE_PARAM_PATTERN, $route) === 1;
        match ($method) {
            RequestMethods::GET => $this->registryForGet[$route] = $entry,
            RequestMethods::POST => $this->registryForPost[$route] = $entry,
        };
    }

    /**
     * Attempts to locate an object associated with a given route.
     *
     * The object defines an action that will be executed for
     * HTTP requests that match the associated route.
     *
     * ### Result matrix
     *
     * 1. Not Parameterized => Target Object
     * 1. Parameterized => Target Object w/ metadata
     * 1. Not Located => `RouteNotRegistered`
     */
    public function match(ServerRequestInterface $request): RoutableInterface | ResolvedRoute | RouteNotRegistered
    {
        $method = $request->getMethod();
        $route = $request->getUri()->getPath();
        return strtoupper($method) === "GET" ? $this->matchGetRoute($route) : $this->matchPostRoute($route);
    }

    private function matchGetRoute(string $route): RoutableInterface | ResolvedRoute | RouteNotRegistered
    {
        return $this->getInstanceFromRegistry($this->registryForGet, $route);
    }

    private function matchPostRoute(string $route): RoutableInterface | ResolvedRoute | RouteNotRegistered
    {
        return $this->getInstanceFromRegistry($this->registryForPost, $route);
    }

    /**
     * @param array<string,RoutableInterface|RoutableFactoryInterface> $registry
     */
    private function getInstanceFromRegistry(array $registry, string $route): RoutableInterface | ResolvedRoute | RouteNotRegistered
    {
        $key = $this->containsParamRoutes === true ? $this->getMatchedParameterizedRoute($registry, $route) : $route;
        if ($key === false) {
            return new RouteNotRegistered();
        }
        if (isset($registry[$key]) === false) {
            return new RouteNotRegistered();
        }
        $targetOrFactory = $registry[$key];
        $target = $targetOrFactory instanceof RoutableFactoryInterface ? $targetOrFactory->createInstance() : $targetOrFactory;
        $this->propertyInjector->inject($target);
        return $this->containsParamRoutes === true ? new ResolvedRoute($target, new RouteParamMap($key, $route)) : $target;
    }

    /**
     * @param array<string,RoutableInterface|RoutableFactoryInterface> $registry
     * @param string $path
     */
    private function getMatchedParameterizedRoute(array $registry, string $path): string|false
    {
        // Reindex the result. See https://www.php.net/manual/en/function.array-filter.php
        return current(array_values(
            array_filter(
                array_keys($registry),
                static fn (string $registeredRoute) => self::partsMatch($registeredRoute, $path),
            )
        ));
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
