<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer;

use ArrayAccess;
use Closure;
use Psr\Container\ContainerInterface;

/**
 * This allows for configuring the container
 * before the server is initialized, afterwards, or both.
 */
abstract class AbstractContainerFactory
{
    /**
     * @param Closure $psr11Factory Creates the PSR-11 container.
     * This allows the user to decide which implementation to use.
     */
    public function __construct(private Closure $psr11Factory)
    {
    }

    /**
     * Retrieve the configured PSR-11 container.
     *
     * @param ArrayAccess<string,mixed> $containerConfig The service/dependency configuration
     * for the container.  The framework will configure its own dependency and any userland
     * dependencies located in `src/config/dependencies/conf.d/` relative to the project
     * root.
     */
    public function getContainer(ArrayAccess $containerConfig): ContainerInterface
    {
        $factory = $this->psr11Factory;
        return $factory($containerConfig);
    }
}
