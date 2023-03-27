<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Routing;

/**
 * Contains route parameters
 */
final class RouteParamMap
{
    /**
     * Holds the route params.
     *
     * @var array<string,string>
     */
    private $internalMap = [];

    private const ROUTE_PARAM_KEY_REPLACEMENT = "$1";

    /**
     * @param string $unresolvedRoute Should represent the parameterized route, i.e. `/some/path/{id}`
     * @param string $requestPath The actual path from the request, i.e. `/some/path/123`
     */
    public function __construct(string $unresolvedRoute, string $requestPath)
    {
        $unresolvedRouteParts = explode("/", ltrim($unresolvedRoute, "/"));
        $requestPathParts = explode("/", ltrim($requestPath, "/"));
        /**
         * @var string[] $paramKeys
         */
        $paramKeys = (array) preg_filter(
            ROUTE_PARAM_PATTERN,
            self::ROUTE_PARAM_KEY_REPLACEMENT,
            $unresolvedRouteParts
        );
        $paramVals = array_intersect_key($requestPathParts, $paramKeys);
        $this->internalMap = array_combine($paramKeys, $paramVals);
    }

    /**
     * Retrieve the entire contents of the route parameter map
     * as an associative array.
     *
     * @return array<string,string>
     */
    public function toArray(): array
    {
        return $this->internalMap;
    }
}
