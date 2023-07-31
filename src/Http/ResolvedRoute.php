<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\Phpolar\Core\Routing\RouteParamMap;
use Phpolar\Routable\RoutableInterface;

/**
 * Represents a route with route parameters.
 *
 * **Example**: `/some/path/{id}`
 *
 * Contains the target object for the route
 * along with a map of the route parameters.
 */
final class ResolvedRoute
{
    public function __construct(
        public RoutableInterface $delegate,
        public RouteParamMap $routeParamMap
    ) {
    }
}
