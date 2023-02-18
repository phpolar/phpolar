<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer;

use ArrayAccess;
use Phpolar\Phpolar\Config\Globs;

/**
 * Configures a dependency injection container.
 */
final class ContainerConfigurator
{
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
     * Add services/dependencies to the provided container.
     *
     * @param ArrayAccess<string,mixed> $containerConfig
     */
    public function configureContainer(ArrayAccess $containerConfig): void
    {
        if (file_exists(Globs::FrameworkDeps->value) === true) {
            $frameworkDeps = require Globs::FrameworkDeps->value;
            array_walk(
                $frameworkDeps,
                self::addDependency(...),
                $containerConfig,
            );
        }
        $globResult = glob(Globs::CustomDeps->value, GLOB_BRACE);
        $customDepConfigs = $globResult === false ? [] : $globResult;
        array_walk(
            $customDepConfigs,
            function (string $filename) use ($containerConfig) {
                $customDepConfs = require_once $filename;
                array_walk(
                    $customDepConfs,
                    self::addDependency(...),
                    $containerConfig,
                );
            },
        );
    }
}
