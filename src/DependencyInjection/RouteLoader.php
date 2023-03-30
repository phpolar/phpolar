<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\DependencyInjection;

use ArrayAccess;
use Closure;
use Phpolar\Phpolar\Routing\RouteRegistry;

/**
 * Adds the route registry to the
 * dependency injection container.
 */
final class RouteLoader
{
    /**
     * Setting up this closure
     * to avoid the readonly property
     * static analyzer
     */
    private Closure $loadConfig;

    /**
     * @param ArrayAccess<string,mixed> $containerConfig
     */
    public function __construct(
        ArrayAccess $containerConfig,
    ) {
        $this->loadConfig = static fn (string $depId, mixed $configuredDep) => $containerConfig[$depId] = $configuredDep;
    }

    /**
     * Add routes to container.
     */
    public function loadRoutes(RouteRegistry $routes): void
    {
        ($this->loadConfig)(RouteRegistry::class, $routes);
    }
}
