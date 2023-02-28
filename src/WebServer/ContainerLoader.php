<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer;

use ArrayAccess;
use Phpolar\Phpolar\Config\Globs;
use Phpolar\Phpolar\Routing\RouteRegistry;

/**
 * Configures a dependency injection container.
 */
final class ContainerLoader
{
    /**
     * @param ArrayAccess<string,mixed> $containerConfig
     */
    public function __construct(private ArrayAccess $containerConfig)
    {
        $globRes1 = glob(Globs::FrameworkDeps->value, GLOB_BRACE);
        array_walk(
            $globRes1,
            function (string $filename): void {
                $frameworkDeps = require $filename;
                array_walk(
                    $frameworkDeps,
                    self::addDependency(...),
                    $this->containerConfig,
                );
            },
        );
        $globResult = glob(Globs::CustomDeps->value, GLOB_BRACE);
        $customDepConfigs = $globResult === false ? [] : $globResult;
        array_walk(
            $customDepConfigs,
            function (string $filename) {
                $customDepConfs = require_once $filename;
                if (is_array($customDepConfs) === false) {
                    return;
                }
                array_walk(
                    $customDepConfs,
                    self::addDependency(...),
                    $this->containerConfig,
                );
            },
        );
    }

    /**
     * @param mixed $configured
     * @param string $depId
     * @param ArrayAccess<string,mixed> $containerConfig
     */
    private static function addDependency(
        mixed $configured,
        string $depId,
        ArrayAccess $containerConfig,
    ): void {
        $containerConfig[$depId] = $configured;
    }

    /**
     * Add routes to container.
     */
    public function loadRoutes(RouteRegistry $routes): void
    {
        self::addDependency($routes, RouteRegistry::class, $this->containerConfig);
    }
}
