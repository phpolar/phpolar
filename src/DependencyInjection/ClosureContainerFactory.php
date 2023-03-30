<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\DependencyInjection;

use ArrayAccess;
use Closure;
use Psr\Container\ContainerInterface;

/**
 * Creates a container using the provided
 * closure.
 */
class ClosureContainerFactory implements ContainerFactoryInterface
{
    /**
     * @param Closure $psr11Factory Creates the PSR-11 container.
     * This allows the user to decide which implementation to use.
     */
    public function __construct(private Closure $psr11Factory)
    {
    }

    /**
     * {@inheritdoc}
     *
     * The framework will configure its own dependency and any userland
     * dependencies located in `src/config/dependencies/conf.d/` relative to the project
     * root.
     */
    public function getContainer(ArrayAccess $containerConfig): ContainerInterface
    {
        return ($this->psr11Factory)($containerConfig);
    }
}
