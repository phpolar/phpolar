<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\DependencyInjection;

use ArrayAccess;
use Closure;
use Psr\Container\ContainerInterface;

/**
 * Configures a dependency injection container.
 */
final class ContainerLoader
{
    /**
     * Configure the container.
     *
     * @param ArrayAccess<string,mixed> $containerConfig
     */
    public function load(
        ContainerInterface $container,
        ArrayAccess $containerConfig,
    ): void {
        $frameworkDepFiles = [
            ...glob(Globs::FrameworkDeps->value),
            ...glob(Globs::UserFrameworkDeps->value),
        ];
        $customDepFiles = [
            ...glob(Globs::CustomDeps->value),
            ...glob(Globs::RootCustomDeps->value),
        ];

        if ($frameworkDepFiles === false || $customDepFiles === false) {
            return; // @codeCoverageIgnore
        }
        $validConfs = array_merge(
            ...array_filter(
                array_map(
                    static fn (string $configFile) => require $configFile,
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
            $containerConfig[$depId] = $configured instanceof Closure ? static fn () => $configured($container) : $configured // @codeCoverageIgnore
        );
    }
}
