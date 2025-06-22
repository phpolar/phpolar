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
        $frameworkDepFiles = glob(Globs::FrameworkDeps->value, GLOB_ERR);
        $userDepFiles = glob(Globs::UserFrameworkDeps->value, GLOB_ERR);
        $customDepFiles = glob(Globs::CustomDeps->value, GLOB_ERR);
        $rootCustomDepFiles = glob(Globs::RootCustomDeps->value);

        $validConfs = array_merge(
            ...array_filter(
                array_map(
                    static fn(string $configFile) => require $configFile,
                    [
                        ...($frameworkDepFiles === false ? [] : $frameworkDepFiles), // @codeCoverageIgnore
                        ...($userDepFiles === false ? [] : $userDepFiles),
                        ...($customDepFiles === false ? [] : $customDepFiles),
                        ...($rootCustomDepFiles === false ? [] : $rootCustomDepFiles),
                    ]
                ),
                is_array(...)
            )
        );

        foreach ($validConfs as $depId => $configured) {
            $dependency = $configured;
            if ($configured instanceof Closure) {
                /**
                 * @suppress PhanUnreferencedClosure
                 * @codeCoverageIgnore
                 */
                $dependency = static fn() => $configured($container);
            }
            $containerConfig[$depId] = $dependency;
        }
    }
}
