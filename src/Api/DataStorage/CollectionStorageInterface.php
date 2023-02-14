<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api\DataStorage;

use Phpolar\Phpolar\Api\Model;

/**
 * Provides a way to interact with a list of items.
 */
interface CollectionStorageInterface
{
    /**
     * Saves an item to a collection.
     *
     * @api
     */
    public function save(Model $record): void;

    /**
     * Returns a list of items.
     *
     * @return Model[]
     *
     * @api
     */
    public function list(string $modelClassName);
}
