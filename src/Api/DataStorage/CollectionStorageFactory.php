<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api\DataStorage;

use Phpolar\Phpolar\Api\Attributes\Config\Collection;

/**
 * Provides a way to create a storage object.
 */
abstract class CollectionStorageFactory
{
    /**
     * Retrieves the storage object.
     *
     * @api
     */
    abstract public function getStorage(Collection $attributeConfig): CollectionStorageInterface;
}
