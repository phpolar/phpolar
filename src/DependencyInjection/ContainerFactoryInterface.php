<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\DependencyInjection;

use ArrayAccess;
use Psr\Container\ContainerInterface;

/**
 * Provides a means to configure the dependency injection
 * before the server is initialized, afterwards, or both.
 */
interface ContainerFactoryInterface
{
    /**
     * Retrieve the configured PSR-11 container.
     *
     * @param ArrayAccess<string,mixed> $containerConfig The service/dependency configuration
     * for the container.
     */
    public function getContainer(ArrayAccess $containerConfig): ContainerInterface;
}
