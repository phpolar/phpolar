<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use Phpolar\Phpolar\Auth\AbstractProtectedRoutable;
use Phpolar\Phpolar\Http\RoutableInterface;

/**
 * Used by the application to determine which
 * routable/handler to use.  This can be
 * used for authenticating routes after
 * the route has been resolved.
 */
interface RoutableResolverInterface
{
    /**
     * Return the given routable or false.
     */
    public function resolve(RoutableInterface $target): RoutableInterface | false;
}
