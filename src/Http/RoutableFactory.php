<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\Routable\RoutableInterface;
use RuntimeException;

/**
 * Provides a type-safe way to provide
 * route targets to the route map that will
 * not be instantiated immediately.
 *
 * Instantiating route targets on demand
 * can improve performance by reducing
 * memory usage.
 */
class RoutableFactory
{
    public function __construct(private string $instanceClassName)
    {
        if (is_subclass_of($instanceClassName, RoutableInterface::class) === false) {
            throw new RuntimeException(
                sprintf(
                    "%s must be an instance of %s",
                    $instanceClassName,
                    RoutableInterface::class
                )
            );
        }
    }

    /**
     * Create the route target
     */
    public function createInstance(): RoutableInterface
    {
        /**
         * @var RoutableInterface
         */
        $instance = new ($this->instanceClassName)();
        return $instance;
    }
}
