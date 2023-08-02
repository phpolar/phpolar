<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\Phpolar\Core\Routing\RouteParamMap;
use Phpolar\Routable\RoutableInterface;

/**
 * Contains a routable that is the target of a route
 * and bound route parameters.
 *
 * **Example**: `/some/path/{id}`
 */
final class ResolvedRoute
{
    public function __construct(
        public RoutableInterface $delegate,
        public RouteParamMap $routeParamMap
    ) {
    }
}
