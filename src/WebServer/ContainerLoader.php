<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer;

use ArrayAccess;
use Closure;
use Phpolar\Phpolar\Config\Globs;
use Phpolar\Phpolar\Routing\RouteRegistry;
use Psr\Container\ContainerInterface;

/**
 * Configures a dependency injection container.
 */
final class ContainerLoader
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
        ContainerInterface $container,
    ) {
        $frameworkDepFiles = glob(Globs::FrameworkDeps->value, GLOB_BRACE);
        $customDepFiles = glob(Globs::CustomDeps->value, GLOB_BRACE);

        if ($frameworkDepFiles === false || $customDepFiles === false) {
            return; // @codeCoverageIgnore
        }
        $validConfs = array_merge(
            ...array_filter(
                array_map(
                    static fn (string $configFile) => require_once $configFile,
                    array_merge(
                        $frameworkDepFiles,
                        $customDepFiles,
                    ),
                ),
                is_array(...)
            )
        );
        array_walk(
            $validConfs,
            static fn (mixed $configured, string $depId) =>
            /**
             * @suppress PhanUnreferencedClosure
             */
            $containerConfig[$depId] = $configured instanceof Closure ? static fn () => $configured($container) : $configured
        );
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
