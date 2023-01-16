<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api\Attributes\Config;

use Phpolar\Phpolar\Core\Attributes\Config\AttributeConfigInterface;

use Closure;

/**
 * Use to register attribute configurations.
 */
final class Collection
{
    /**
     * @var array<string,AttributeConfigInterface>
     * @internal
     */
    private array $internalArray = [];

    /**
     * Adds a configuration to this registry.
     *
     * @api
     */
    public function add(Key $key, AttributeConfigInterface $attribute): void
    {
        $this->internalArray[$key->getKey()] = $attribute;
    }

    /**
     * Applies a filtering function to this registry.
     *
     * @api
     */
    public function filter(Closure $filter): Collection
    {
        $clone = clone $this;
        $clone->internalArray = array_filter($clone->internalArray, $filter);
        return $clone;
    }

    /**
     * Transforms each configuration in this registry by
     * applying the given mapping function.
     *
     * @api
     */
    public function map(Closure $fun): Collection
    {
        $newCollection = clone $this;
        $newCollection->internalArray = array_map(
            $fun,
            array_keys($this->internalArray),
            array_values($this->internalArray)
        );
        return $newCollection;
    }

    /**
     * Converts this object into an array.
     *
     * @api
     */
    public function toArray(): array
    {
        return $this->internalArray;
    }
}
