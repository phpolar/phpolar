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

    private bool $containsParamRoutes = false;

    /**
     * Associates a request handler to a request.
     */
    public function add(string $method, string $route, AbstractContentDelegate $handler): void
    {
        if (strtoupper($method) === "GET") {
            $this->registryForGet[$route] = $handler;
            $this->containsParamRoutes = $this->containsParamRoutes || preg_match(ROUTE_PARAM_PATTERN, $route) === 1;
            return;
        }
        $this->registryForPost[$route] = $handler;
    }

    /**
     * Retrieves the registered handler for a request.
     */
    public function match(ServerRequestInterface $request): AbstractContentDelegate | ResolvedRoute | RouteNotRegistered
    {
        $method = $request->getMethod();
        $route = $request->getUri()->getPath();
        return strtoupper($method) === "GET" ? $this->matchGetRoute($route) : ($this->registryForPost[$route] ?? new RouteNotRegistered());
    }

    private function matchGetRoute(string $route): AbstractContentDelegate | ResolvedRoute | RouteNotRegistered
    {
        return $this->registryForGet[$route] ?? $this->matchAnyParameterizedRoute($route);
    }

    private function matchAnyParameterizedRoute(string $path): ResolvedRoute | RouteNotRegistered
    {
        if ($this->containsParamRoutes === false) {
            return new RouteNotRegistered();
        }
        $pathParts = explode("/", ltrim($path, "/"));
        /**
         * @var string[]
         */
        $registeredRoutes = array_keys($this->registryForGet);
        // @codeCoverageIgnoreStart
        foreach ($registeredRoutes as $registeredRoute) {
            $regRouteParts = explode("/", ltrim($registeredRoute, "/"));
            $regRoutePrtCnt = count($regRouteParts);
            $routePrtCnt = count($pathParts);
            if ($regRoutePrtCnt !== $routePrtCnt) {
                continue;
            }
            $matched = [];
            for ($i = 0; $i < $regRoutePrtCnt; $i++) {
                if (self::partsMatch($regRouteParts[$i], $pathParts[$i]) === false) {
                    continue 2;
                }
                $matched[] = $regRouteParts[$i];
            }
            $routeToTest = "/" . implode("/", $matched);
            if (isset($this->registryForGet[$routeToTest]) === true) {
                return new ResolvedRoute(
                    $this->registryForGet[$routeToTest],
                    new RouteParamMap($routeToTest, $path)
                );
            }
        }
        // @codeCoverageIgnoreEnd
        return new RouteNotRegistered();
    }

    private static function partsMatch(string $regRoutePart, string $pathPart): bool
    {
        return $regRoutePart === $pathPart || preg_match(ROUTE_PARAM_PATTERN, $regRoutePart) === 1;
    }
}
