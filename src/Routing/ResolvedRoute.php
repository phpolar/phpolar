<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Routing;

use Phpolar\Phpolar\Core\Routing\RouteParamMap;

/**
 * Represents a route with route parameters.
 *
 * **Example**: `/some/path/{id}`
 *
 * Contains the delegate for the route
 * along with a map of the route parameters.
 */
final class ResolvedRoute
{
    public function __construct(
        public AbstractContentDelegate $delegate,
        public RouteParamMap $routeParamMap
    ) {
    }
}
