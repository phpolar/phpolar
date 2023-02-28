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
        $globRes2 = glob(Globs::CustomDeps->value, GLOB_BRACE);
        $frameworkDepConfs = $globRes1 === false ? [] : $globRes1;
        $customDepConfs = $globRes2 === false ? [] : $globRes2;
        $configFiles = array_merge(
            $frameworkDepConfs,
            $customDepConfs,
        );
        array_walk(
            $configFiles,
            $this->addDepsFromFile(...),
        );
    }

    private function addDepsFromFile(string $filename): void
    {
        $confs = require_once $filename;
        if (is_array($confs) === false) {
            return;
        }
        array_walk(
            $confs,
            self::addDependency(...),
            $this->containerConfig,
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
