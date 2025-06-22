<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

/**
 * Contains route parameters
 */
final class PathVariableBindings
{
    /**
     * Holds the route params.
     *
     * @var array<string,string>
     */
    private $internalMap = [];

    private const PATH_VARIABLE_KEY_REPLACEMENT = "$1";
    public const PATH_VARIABLE_PATTERN = "/\{([[:alpha:]]+)\}/";

    /**
     * @param string $unresolvedRoute Should represent the parameterized route, i.e. `/some/path/{id}`
     * @param string $requestPath The actual path from the request, i.e. `/some/path/123`
     */
    public function __construct(string $unresolvedRoute, string $requestPath)
    {
        if ($this->containsPathVariables($unresolvedRoute) === true) {
            $unresolvedRouteParts = explode("/", ltrim($unresolvedRoute, "/"));
            $requestPathParts = explode("/", ltrim($requestPath, "/"));
            /**
             * @var string[] $paramKeys
             */
            $paramKeys = (array) preg_filter(
                self::PATH_VARIABLE_PATTERN,
                self::PATH_VARIABLE_KEY_REPLACEMENT,
                $unresolvedRouteParts
            );
            $paramVals = array_intersect_key($requestPathParts, $paramKeys);
            $paramValsDecoded = array_map(urldecode(...), $paramVals);
            $this->internalMap = array_combine($paramKeys, $paramValsDecoded);
        }
    }

    private function containsPathVariables(string $path): bool
    {
        return preg_match(self::PATH_VARIABLE_PATTERN, $path) === 1;  // @codeCoverageIgnore
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
